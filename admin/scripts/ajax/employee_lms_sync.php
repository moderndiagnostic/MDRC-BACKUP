<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//employee sync
$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://lis6.mdrcindia.com/mdrcnew/api/BookingAPI/GetEmployee',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS =>'{"EmployeeID":"","PageNo":1}',
CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
),
));
$response = curl_exec($curl);
curl_close($curl);

$res=json_decode($response,true);

$positions=['Director','Vice President - Sales','General Manager','Deputy General Manager','Zonal Sales Manager','Regional Sales Manager','Area Sales Manager','Business  Development Executive','Logistics Manager','Logistics','Field Boy'];
foreach($res['data'] as $item)
{
    if(in_array(strtolower($item['Designation']), array_map('strtolower',$positions))) {
        //city add in master
        if($item['city']!=''){
            $obj_model_city=$app->load_model("city");
            $cityR=$obj_model_city->execute("SELECT",false,"","name='".$item['city']."'");
            if(count($cityR)<=0) {
                $data=array();
                $data['name']=$item['city'];
                $data['slug']=$app->utility->seo_url($item['city']);
                $data['status']='Active';
                $data['created_at']=date("Y-m-d H:i:s");
                $obj_model_city=$app->load_model("city");
                $obj_model_city->map_fields($data);
                $cityID=$obj_model_city->execute("INSERT");
            } else {
                $cityID=$cityR[0]['id'];
            }
        } else {
            $cityID=0;
        }

        if($item['Designation']!=''){
            $obj_model_city=$app->load_model("master_designation");
            $master_designation=$obj_model_city->execute("SELECT",false,"","name='".$item['Designation']."'");
            if(count($master_designation)<=0) {
                $data=array();
                $data['name']=$item['Designation'];
                $data['status']='Active';
                $data['created_at']=date("Y-m-d H:i:s");
                $data['updated_at']=date("Y-m-d H:i:s");
                $obj_model_master_designation=$app->load_model("master_designation");
                $obj_model_master_designation->map_fields($data);
                $masterDesignationID=$obj_model_master_designation->execute("INSERT");
            } else {
                $masterDesignationID=$master_designation[0]['id'];
            }
        } else {
            $masterDesignationID=0;
        }

        //check if employer exist or not
        $obj_model_employee=$app->load_model("employee");
        $employee=$obj_model_employee->execute("SELECT",false,"","lms_employee_id='".$item['Employee_id']."'");
        
        if(count($employee)>0) {
            //update
            $data_t=array();
            $data_t['lms_employee_id']=$item['Employee_id'];
            $data_t['lms_employee_code']=$item['Emoloyee_Code'];
            $data_t['name']=$item['EmoloyeeName'];
            $data_t['email']=$item['email'];
            $data_t['mobile']=$item['mobile'];
            $data_t['master_designation_id']=$masterDesignationID;
            $data_t['city_id']=$cityID;
            $data_t['status']=$item['isactive']==1?'Active':'Inactive';
            $data_t['reporting_employee_lms_id']=$item['Reporting_Employee_ID'];
            $data_t['updated_at']=date("Y-m-d H:i:s");
            $obj_model_employee=$app->load_model("employee");
            $obj_model_employee->map_fields($data_t);
            $obj_model_employee->execute("UPDATE",false,"","id='".$employee[0]['id']."'");

            $data_t=array();
            $data_t['area']=$item['Locality'];
            $data_t['master_centre_lms_ids']=$item['Tagcentreid'];
            $data_t['updated_at']=date("Y-m-d H:i:s");
            $obj_model_employee_detail=$app->load_model("employee_detail");
            $obj_model_employee_detail->map_fields($data_t);
            $obj_model_employee_detail->execute("UPDATE",false,"","employee_id='".$employee[0]['id']."'");

            //delete old
            $obj_model_employee_centre=$app->load_model("employee_centre");
            $obj_model_employee_centre->execute("DELETE",false,"","employee_id='".$employee[0]['id']."'");

            $Tagcentreid=explode(',',$item['Tagcentreid']);
            foreach($Tagcentreid as $key=>$value)
            {
                $data_t=[];
                $data_t['employee_id']=$employee[0]['id'];
                $data_t['lms_centre_id']=$value;
                $obj_model_employee_centre=$app->load_model("employee_centre");
                $obj_model_employee_centre->map_fields($data_t);
                $obj_model_employee_centre->execute("INSERT");
            }

        } else {
            //insert
            $data_t=array();
            $data_t['lms_employee_id']=$item['Employee_id'];
            $data_t['lms_employee_code']=$item['Emoloyee_Code'];
            $data_t['name']=$item['EmoloyeeName'];
            $data_t['email']=$item['email'];
            $data_t['mobile']=$item['mobile'];
            $data_t['master_designation_id']=$masterDesignationID;
            $data_t['city_id']=$cityID;
            $data_t['login_password']='Admin';
            $data_t['status']=$item['isactive']==1?'Active':'Inactive';
            $data_t['reporting_employee_lms_id']=$item['Reporting_Employee_ID'];
            $data_t['created_at']=date("Y-m-d H:i:s");
            $data_t['updated_at']=date("Y-m-d H:i:s");
            $obj_model_employee=$app->load_model("employee");
            $obj_model_employee->map_fields($data_t);
            $employeeId=$obj_model_employee->execute("INSERT");

            $data_t=array();
            $data_t['employee_id']=$employeeId;
            $data_t['area']=$item['Locality'];
            $data_t['master_centre_lms_ids']=$item['Tagcentreid'];
            $data_t['updated_at']=date("Y-m-d H:i:s");
            $obj_model_employee_detail=$app->load_model("employee_detail");
            $obj_model_employee_detail->map_fields($data_t);
            $obj_model_employee_detail->execute("INSERT");

            $Tagcentreid=explode(',',$item['Tagcentreid']);
            foreach($Tagcentreid as $key=>$value)
            {
                $data_t=[];
                $data_t['employee_id']=$employeeId;
                $data_t['lms_centre_id']=$value;
                $obj_model_employee_centre=$app->load_model("employee_centre");
                $obj_model_employee_centre->map_fields($data_t);
                $obj_model_employee_centre->execute("INSERT");
            }
        }
    }	
}
//employee end

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