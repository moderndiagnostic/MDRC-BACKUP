<?php

$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);
//get action
$selectEmployeeId = $app->getPostVar("selectEmployeeId");
$employeeID = $app->getPostVar("employeeID");
$level = $app->getPostVar("level");
$search=$app->getPostVar("search");

$login_lms_employee_id = $app->getPostVar("login_lms_employee_id")??1;
$page = $app->getPostVar("page");
$limit = $app->getPostVar("limit");
$start=$page==0?0:($page)*$limit;
$whereCond='';
if($search!='') {
    $whereCond.=" and (client.company_name LIKE '%$search%' or client.mobile LIKE '%$search%')";
}

if (!empty($selectEmployeeId)) {

    $obj_model_client=$app->load_model("employee");
    $employee=$obj_model_client->execute("SELECT",false,"","id='".$app->utility->decrypt($selectEmployeeId)."'");
    
    $data=$app->utility->getSubEmployee($employee[0]['lms_employee_id']);
   
    $employeeLmsId=count($data)>0?array_column($data,'lms_employee_id'):[];
    array_push($employeeLmsId,$employee[0]['lms_employee_id']);

    $obj_model_client=$app->load_model("client");
    $clientCount=$obj_model_client->execute("SELECT",false,"","client.status='Active' and lms_employee_id In(".implode(',',$employeeLmsId).") ".$whereCond."");
   
    $obj_model_client=$app->load_model("client");
    $obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
    $obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
    $obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
    $client=$obj_model_client->execute("SELECT",false,"","client.status='Active' and lms_employee_id In(".implode(',',$employeeLmsId).") ".$whereCond."","client.id desc limit ".$start.",".$limit."");

    $count=count($clientCount);

    $displayRecord=count($clientCount);
  
    $html='';
    //$url='index.php?view=webview_my_team&employeeID='.$id.'&employeePhone='.$mobile.'&level='.($level+1).'&selectedEmployeeId=';
   
    if(count($client)>0){
        foreach($client as $item) 
        {
            $url='index.php?view=webview_client_detail&employeeID='.$employeeID.'&clientID='.$app->utility->encrypt($item['id']).'&selectEmployeeId='.$selectEmployeeId;
            if(!empty($item["image"])){
                $image=$app->utility->get_image_url($item["image"],'employee','large');
            }else{
                $image= SERVER_ROOT."/uploads/user.png";
            }
            $address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
            $html.= ' <div class="card-lists client-list">
                    <a href="'.$url.'" class="" style="text-decoration:none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <img src="'.$image.'" alt="" class="avtar-md">
                                    </div>
                                    <div class="ms-2">
                                        <h6 class="text-main mb-0 line-clamp-1">'.$item["company_name"].'</h6>';
                                        if(!empty($address)){
                                           $html.= ' <div class="d-flex align-items-start">
                                                        <img src="assets/images/svg/location.svg" alt="" class="mt-1">
                                                        <p class="mb-0 content-color ms-1 line-clamp-2">'.$address.'</p>
                                                    </div>';
                                        }
                                        
                                   $html.= ' </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>';
        }
    }
    else
    {
        $html.= '<div class="team-list card-lists">
        <div class="card">
            <div class="card-anchor">
                <div class="card-body">
                    <p class="text-center">No Data Found.</p>
                </div>
            </div>
        </div>
    </div>';
    }
    
   
    $msg = "success";
    $msgcode = '1';
    echo $obj_json->encode(array("RESULT" => $msgcode, "html" => $html, "msg" => $msg,'totalItems'=>$count));
    exit;
} else {
    //email is blank
    $msg = "We couldn't find a user with that email address.";
    $msgcode = '0';
}
echo $obj_json->encode(array("RESULT" => $msgcode, "url" => "", "msg" => $msg));
