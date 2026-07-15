<?php

$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);
//get action
$id = $app->utility->encrypt($app->getPostVar("selectEmployeeId"));
$employeeID = $app->getPostVar("employeeID");
$level = $app->getPostVar("level");
$search=$app->getPostVar("search");

$selectEmployeeLmsId = $app->getPostVar("selectEmployeeLmsId");
$page = $app->getPostVar("page");
$limit = $app->getPostVar("limit");
$start=$page==0?0:($page)*$limit;
$whereCond='';

if($search!='') {
    $whereCond.=" and (employee.name LIKE '%$search%' or employee.mobile LIKE '%$search%')";
}

if (!empty($selectEmployeeLmsId)) {

    $obj_model_employee = $app->load_model("employee");
    $employeeCount = $obj_model_employee->execute("SELECT",false,"","employee.status='Active' and lms_employee_id!='".$selectEmployeeLmsId."' and reporting_employee_lms_id='".$selectEmployeeLmsId."' ".$whereCond."");
   

    $obj_model_employee = $app->load_model("employee");
    $obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
	$employee = $obj_model_employee->execute("SELECT",false,"","employee.status='Active' and lms_employee_id!='".$selectEmployeeLmsId."' and employee.reporting_employee_lms_id='".$selectEmployeeLmsId."' ".$whereCond."","employee.id desc limit ".$start.",".$limit."");
   
    $count=count($employeeCount);

    $html='';
  
    //$url='index.php?view=webview_my_team&employeeID='.$id.'&employeePhone='.$mobile.'&level='.($level+1).'&selectedEmployeeId=';
    
    if(count($employee)>0){
        foreach($employee as $item) {
            $startOfMonth = date('Y-m-01'); 
            $endOfMonth = date('Y-m-t');

            $obj_model_employee_task = $app->load_model("employee_task_master");
            $task = $obj_model_employee_task->execute("SELECT",false,"","employee_task_master.employee_primary_id='".$item['id']."' and created_at BETWEEN '$startOfMonth' AND '$endOfMonth'");

            if(!empty($item["image"])){
                $image=$app->utility->get_image_url($item["image"],'employee','large');
            }else{
                $image= SERVER_ROOT."/uploads/user.png";
            }
        $data=$app->utility->getSubEmployee($item["lms_employee_id"]);
       
        $client=$app->utility->getClientDetail($data,$item["lms_employee_id"]);
        if(count($data)==0){
            $url='index.php?view=webview_client_list&employeeID='.$employeeID.'&employeePhone='.$mobile.'&level='.($level+1).'&selectEmployeeId=';
        }
        else{
            $url='index.php?view=webview_my_team&employeeID='.$employeeID.'&employeePhone='.$mobile.'&level='.($level+1).'&selectEmployeeId=';
        }
        $ClientListUrl='index.php?view=webview_client_list&employeeID='.$employeeID.'&employeePhone='.$mobile.'&level='.($level+1).'&selectEmployeeId=';
        $TaskListUrl='index.php?view=webview_task_list&employeeID='.$employeeID.'&employeePhone='.$mobile.'&level='.($level+1).'&selectEmployeeId=';
        $html.= '<div class="team-list card-lists">
                    <div class="card">
                        <a href="'. $url.$app->utility->encrypt($item['id']).'" class="card-anchor">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <img src="'.$image.'" alt="">
                                    </div>
                                    <div class="ms-2">
                                        <h6 class="text-main mb-0 line-clamp-1">'.$item['name'].'</h6>
                                        <p class="mb-0 content-color line-clamp-1">'.$item['master_designation_name'].'</p>
                                        <span class="fw-bold tx-12">'.$item['mobile'].'</span>
                                    </div>
                                    <div class="ms-auto arrow-box">
                                        <i class="fa-solid fa-arrow-right-long"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="card-footer bg-white py-0">
                            <div class="row align-items-center justify-content-between py-2">
                                <a href="'. $ClientListUrl.$app->utility->encrypt($item['id']).'" class="col-4 border-end d-flex justify-content-center align-items-center text-decoration-none ">
                                      <img src="assets/images/svg/team.svg" alt="">
                                        <p class="mb-0 tx-14 ms-1 text-main ">Active Client : '.$client.'</p>
                                 </a>
                                <a href="'. $url.$app->utility->encrypt($item['id']).'" class="col-4 d-flex border-end justify-content-center align-items-center text-decoration-none">
                                    
                                        <img src="assets/images/svg/team.svg" alt="">
                                        <p class="mb-0 tx-14 ms-1 text-main">Total Team : '.count($data).'</p>
                                    
                                </a>
                                <a href="'. $TaskListUrl.$app->utility->encrypt($item['id']).'" class="col-4 border-end d-flex justify-content-center align-items-center text-decoration-none">
                                      <img src="assets/images/svg/team.svg" alt="">
                                        <p class="mb-0 tx-14 ms-1 text-main ">Current Month Task : '.count($task).'</p>
                                 </a>
                            </div>
                        </div>
                    </div>
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
