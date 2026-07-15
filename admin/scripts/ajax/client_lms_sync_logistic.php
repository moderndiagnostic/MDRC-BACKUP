<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

$obj_model_state=$app->load_model("client");
$client=$obj_model_state->execute("SELECT",false,"","panel_id>0 and status='Active'","id desc limit 6000,1000");
foreach($client as $item){
   
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://182.156.200.228/mdrcnew/api/BookingAPI/GetPanel',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'PanelID='.$item['panel_id'],
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    if(!empty($response)){
        $responseData=json_decode($response,true);
        if($responseData['status'] && !empty($responseData['data'][0]['LogisticBoy'])){

            $employeeLMSArray=explode(' - ',$responseData['data'][0]['LogisticBoy']);
            $employeeLMSId=$employeeLMSArray[0];

            if(!empty($employeeLMSId)){
                
                $obj_model_employee=$app->load_model("employee");
                $employee=$obj_model_employee->execute("SELECT",false,"","lms_employee_code='".$employeeLMSId."'");

                if(count($employee)>0)
                {
                    $obj_model_client_logistic_assign=$app->load_model("client_logistic_assign");
                    $client_logistic_assign=$obj_model_client_logistic_assign->execute("SELECT",false,"","client_id=".$item['id']);
                    if(count($client_logistic_assign)>0){
                        $data=array();
                        $data['logistic_manager_employee_id']='29';
                        $data['employee_id']=$employee[0]['id'];
                        $data['assign_by_employee_id']='29';
                        $data['request_status']='Active';
                        $obj_model_c=$app->load_model("client_logistic_assign");
                        $obj_model_c->map_fields($data);
                        $obj_model_c->execute("UPDATE",false,"","id='".$client_logistic_assign[0]['id']."'");
                    }else{
                        $data=array();
                        $data['client_id']=$item['id'];
                        $data['logistic_manager_employee_id']='29';
                        $data['employee_id']=$employee[0]['id'];
                        $data['assign_by_employee_id']='29';
                        $data['request_status']='Active';
                        $data['created_at']=date("Y-m-d H:i:s");
                        $obj_model_state=$app->load_model("client_logistic_assign");
                        $obj_model_state->map_fields($data);
                        $obj_model_state->execute("INSERT");
                    }
                }
            }
        }
    }
}

$msg="Data Sync Successfully.";
$msgcode='1';
echo $obj_json->encode(array("RESULT"=>$msgcode,"msg"=>$msg));
exit;






//client sync
$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => 'http://182.156.200.228/mdrcnew/api/BookingAPI/GetPanel',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS =>'{"PanelID":""}',
CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
),
));

$response = curl_exec($curl);
curl_close($curl);
$res=json_decode($response,true);

foreach($res['data'] as $item)
{
    $stateID='';
    $cityID='';
    $businesszoneID='';

    if($item['State']!='') {
        $obj_model_state=$app->load_model("state");
        $state=$obj_model_state->execute("SELECT",false,"","name='".$item['State']."'");
        if(count($state)<=0) {
            $data=array();
            $data['name']=$item['State'];
            $data['status']='Active';
            $data['created_at']=date("Y-m-d H:i:s");
            $obj_model_state=$app->load_model("state");
            $obj_model_state->map_fields($data);
            $stateID=$obj_model_state->execute("INSERT");
        } else {
            $stateID=$state[0]['id'];
        }
    }

    if($item['City']!=''){
        $obj_model_city=$app->load_model("city");
        $cityR=$obj_model_city->execute("SELECT",false,"","name='".$item['City']."'");
        if(count($cityR)<=0) {
            $data=array();
            $data['state_id']=$stateID;
            $data['name']=$item['City'];
            $data['slug']=$app->utility->seo_url($item['City']);
            $data['status']='Active';
            $data['created_at']=date("Y-m-d H:i:s");
            $obj_model_city=$app->load_model("city");
            $obj_model_city->map_fields($data);
            $cityID=$obj_model_city->execute("INSERT");
        } else {
            $cityID=$cityR[0]['id'];
        }
    }

    if($item['BusinessZone']!=''){ 
        $obj_model_master_businesszone=$app->load_model("master_businesszone");
        $businesszone=$obj_model_master_businesszone->execute("SELECT",false,"","name='".$item['BusinessZone']."'");
        if(count($businesszone)<=0) {
            $data=array();
            $data['name']=$item['BusinessZone'];
            $data['status']='Active';
            $data['created_at']=date("Y-m-d H:i:s");
            $data['updated_at']=date("Y-m-d H:i:s");
            $obj_model_master_businesszone=$app->load_model("master_businesszone");
            $obj_model_master_businesszone->map_fields($data);
            $businesszoneID=$obj_model_master_businesszone->execute("INSERT");
        } else {
            $businesszoneID=$businesszone[0]['id'];
        }
    }

    $stateID=$item['State']==''?0:$stateID;
    $cityID=$item['City']==''?0:$cityID;
    $businesszoneID=$item['BusinessZone']==''?0:$businesszoneID;

    //check if client exist or not
    $obj_model_client=$app->load_model("client");
    $client=$obj_model_client->execute("SELECT",false,"","panel_id='".$item['Panel_ID']."'");
    
    if(count($client)>0) 
    {
        //update
        $data_t=array();
        $data_t['panel_id']=$item['Panel_ID'];
        $data_t['company_name']=$item['Company_Name'];
        $data_t['phone']=$item['Phone'];
        $data_t['mobile']=$item['Mobile'];
        $data_t['city_id']=$cityID;
        $data_t['state_id']=$stateID;
        $data_t['master_businesszone_id']=$businesszoneID;
        $data_t['status']=$item['isactive']==1?'Active':'Inactive';
        $data_t['lms_employee_id']=$item['SalesManagerID'];
        $data_t['created_at']=date("Y-m-d H:i:s");
        $data_t['updated_at']=date("Y-m-d H:i:s");
        $obj_model_employee=$app->load_model("client");
        $obj_model_employee->map_fields($data_t);
        $obj_model_employee->execute("UPDATE",false,"","id='".$client[0]['id']."'");

        $data_t=array();
        $data_t['address']=$item['Add1'];
        $data_t['area']=$item['AREA'];
        $data_t['ledger_report_password']=$item['LedgerReportPassword'];
        $data_t['invoice_to_center']=$item['InvoiceTo'];
        $data_t['booking_lock_reason']=$item['BookingLockReason'];
        $data_t['credit_limit']=$item['CreditLimit'];
        $data_t['is_printing_lock']=$item['IsPrintingLock'];
        $obj_model_employee_detail=$app->load_model("client_detail");
        $obj_model_employee_detail->map_fields($data_t);
        $obj_model_employee_detail->execute("UPDATE",false,"","client_id='".$client[0]['id']."'");

    } 
    else 
    {
        //insert
        $data_t=array();
        $data_t['panel_id']=$item['Panel_ID'];
        $data_t['company_name']=$item['Company_Name'];
        $data_t['phone']=$item['Phone'];
        $data_t['mobile']=$item['Mobile'];
        $data_t['city_id']=$cityID;
        $data_t['state_id']=$stateID;
        $data_t['master_businesszone_id']=$businesszoneID;
        $data_t['status']=$item['isactive']==1?'Active':'Inactive';
        $data_t['lms_employee_id']=$item['SalesManagerID'];
        $data_t['created_at']=date("Y-m-d H:i:s");
        $data_t['updated_at']=date("Y-m-d H:i:s");
        $obj_model_employee=$app->load_model("client");
        $obj_model_employee->map_fields($data_t);
        $clientId=$obj_model_employee->execute("INSERT");

        $data_t=array();
        $data_t['client_id']=$clientId;
        $data_t['address']=$item['Add1'];
        $data_t['area']=$item['AREA'];
        $data_t['ledger_report_password']=$item['LedgerReportPassword'];
        $data_t['invoice_to_center']=$item['InvoiceTo'];
        $data_t['booking_lock_reason']=$item['BookingLockReason'];
        $data_t['credit_limit']=$item['CreditLimit'];
        $data_t['is_printing_lock']=$item['IsPrintingLock'];
        $obj_model_employee_detail=$app->load_model("client_detail");
        $obj_model_employee_detail->map_fields($data_t);
        $obj_model_employee_detail->execute("INSERT");

        $data_t=array();
        $data_t['client_id']=$clientId;
        $data_t['updated_at']=date("Y-m-d H:i:s");
        $obj_model_employee_detail=$app->load_model("client_address");
        $obj_model_employee_detail->map_fields($data_t);
        $obj_model_employee_detail->execute("INSERT");

        $data_t=array();
        $data_t['client_id']=$clientId;
        $data_t['created_at']=date("Y-m-d H:i:s");
        $obj_model_employee_detail=$app->load_model("client_bank");
        $obj_model_employee_detail->map_fields($data_t);
        $obj_model_employee_detail->execute("INSERT");

        $data_t=array();
        $data_t['client_id']=$clientId;
        $data_t['created_at']=date("Y-m-d H:i:s");
        $data_t['updated_at']=date("Y-m-d H:i:s");
        $obj_model_employee_detail=$app->load_model("client_files");
        $obj_model_employee_detail->map_fields($data_t);
        $obj_model_employee_detail->execute("INSERT");
    }
}
//client sync end

if($response!='')
{
    $msg="Data Sync Successfully.";
    $msgcode='1';
} 
else 
{
    $msg="Try Again After Sometime.";
    $msgcode='0';
}

echo $obj_json->encode(array("RESULT"=>$msgcode,"msg"=>$msg));
?>