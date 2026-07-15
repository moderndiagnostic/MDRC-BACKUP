<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active employee datatbale loading
if($get_actionType=="employee_finance_manager_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");
	$designation_id=$app->getPostVar("designation_id");

	$table_name='employee_journey_finance_manager';

	## Read value
	$draw = $app->getPostVar('draw');
	$row = $app->getPostVar('start');
	$rowperpage = $app->getPostVar('length'); // Rows display per page
	$orderArray = $app->getPostVar('order');
	$columnIndex = $orderArray[0]['column']; // Column index
	
	$columnArray = $app->getPostVar('columns');
	$columnName = $columnArray[$columnIndex]['data']; // Column name
	
	if($columnName=='checkbox' || $columnName=='btn' || $columnName=='image')
	{
		$columnName='id';
	}
	
	$columnSortOrder = $orderArray[0]['dir']; // asc or desc
	
	$searchArray=$app->getPostVar('search');
	$searchValue = $searchArray['value']; // Search value
	
	## Search 
	$searchQuery = " ";
	if($searchValue != '')
	{
		$searchQuery = " and (	
		employee_journey_finance_manager.id like '%".$searchValue."%' or
		employee.name like '%".$searchValue."%' or
		employee.lms_employee_code like '%".$searchValue."%'
		) ";
	}

	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".status!='Trash'");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." left join employee on employee_journey_finance_manager.employee_id = employee.id where ".$table_name.".status!='Trash' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_table->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".status!='Trash' ".$searchQuery."",$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage,$groupBy);
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$obj_table_count = $app->load_model("employee_journey_finance_submanager");
		$results = $obj_table_count->execute("SELECT", false, "SELECT count(*) as allcount from employee_journey_finance_submanager where employee_journey_finance_manager_id='".$result[$i]['id']."'");
		$delete_btn='<a data-toggle="tooltip" data-placement="top" title="Delete" href="javascript:void(0)" data-id="'.$result[$i]['id'].'" class="btn btn-xs btn-danger btn-icon mg-r-5 employee_finance_manager_delete_onclick"><i class="fas fa-trash"></i></a>';
		$detail_btn='<a data-toggle="tooltip" data-placement="top" title="Finance Manager Assign" href="javascript:void(0)" data-id="'.$result[$i]['id'].'" class="btn btn-xs btn-warning btn-icon mg-r-5 employee_finance_manager_assign_onclick"><i class="fas fa-thumbtack"></i></a>';
		$option='<div class="btn-toolbar"><div>'.$detail_btn.' '.$delete_btn.'</div></div>';
				
		//data
		$data[] = array
		(
			"id"=>$result[$i]["id"],
			"employee_name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_lms_employee_code'],
			"manager_count"=>$results[0]['allcount'],
			"created_at"=>date('d-m-Y h:i A',strtotime($result[$i]['created_at'])),
			"btn"=>$option
		);
	}
	
	## Response
	$response = array(
		"draw" => $draw,
		"iTotalRecords" => $totalRecords,
		"iTotalDisplayRecords" => $totalRecordwithFilter,
		"aaData" => $data
	);
		
	echo json_encode($response);
	exit;
}

if($get_actionType=="employeeSearch")
{
	$search=$app->getGetVar('search');

    $obj_model_employee = $app->load_model("employee");
	$cond_product = "AND (name LIKE '%" . $search . "%' OR lms_employee_code LIKE '%" . $search . "%')";
    $rs_employee = $obj_model_employee->execute("SELECT", false, "", "name != '' and status = 'Active' " . $cond_product."LIMIT 20");

    $autocomplete_results = [];
    foreach ($rs_employee as $product) {

        $autocomplete_results[] = [
            'label' => $product['name'] . ' - ' . $product['lms_employee_code'],
            'id' => $product['id'],
            'lms_employee_code' => $product['lms_employee_code'],
        ];
    }
    echo $obj_json->encode($autocomplete_results); exit;
}

if($actionType=="financeManagerAddEdit")
{
	$employee_ids=$app->getPostVar('selected_id');
	$employee_idStr=implode(",", $employee_ids);
	if(empty($employee_idStr)){
		$msg="Select Finance Manager First.";
		$msgcode=1;
		echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg)); exit;
	}
	$obj_check = $app->load_model("employee_journey_finance_manager");
	$result = $obj_check->execute("SELECT", false, "", "status!='Trash' and employee_id IN ($employee_idStr)");
	if(count($result)>0){
		$msg="Finance Manager Already Added.";
		$msgcode=1;
		echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg)); exit;
	}
	
	foreach($employee_ids as $emp)
	{
		$obj_emp_name = $app->load_model("employee");
		$resultF = $obj_emp_name->execute("SELECT", false, "", "id='".$emp."'");

		$update_field = array();
		$update_field['city_id'] = 0;
		$update_field['employee_id'] = $emp;
		$update_field['employee_name'] = $resultF[0]['name'];
		$update_field['status'] = 'Active';
		$obj_model_user = $app->load_model("employee_journey_finance_manager");
		$obj_model_user->map_fields($update_field);
		$ins=$obj_model_user->execute("INSERT",false,"","");
	}

	$msg="Employee Finance Manager Successfully.";
	$msgcode=0;
}

if($actionType=="employeeFinanceAssignAddEdit")
{
	$id=$app->getPostVar('id');
	$employee_ids=$app->getPostVar('employee_ids');

	$obj_model_employee = $app->load_model("employee_journey_finance_submanager");
    $rs_employee = $obj_model_employee->execute("DELETE", false, "", "employee_journey_finance_manager_id = '".$id."'");

	foreach($employee_ids as $emp)
	{
		$update_field = array();
		$update_field['employee_journey_finance_manager_id'] = $id;
		$update_field['employee_id'] = $emp;
		$obj_model_user = $app->load_model("employee_journey_finance_submanager");
		$obj_model_user->map_fields($update_field);
		$ins=$obj_model_user->execute("INSERT",false,"","");
	}

	$msg="Employee Finance Manager Assign Successfully.";
	$msgcode=0;
}

if($actionType=="employeeFinanceManagerDelete")
{
	$getid=$app->getPostVar('getid');
	if($getid!= NULL && $getid>0)
	{
		$update_field = array();
		$update_field['status']= 'Trash';
		$obj_change_table = $app->load_model('employee_journey_finance_manager');
		$obj_change_table->map_fields($update_field);
		$update_id = $obj_change_table->execute("UPDATE",false,"","id='".$getid."'");
		if($update_id>0)
		{
			$msg='Sucess';
			$msgcode=0;
		}
		else
		{
			$msg='Please Try Again.';
			$msgcode=1;
		}
	}
	else
	{
		$msg='Please Try Again.';
		$msgcode=1;
	}
}

echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
?>