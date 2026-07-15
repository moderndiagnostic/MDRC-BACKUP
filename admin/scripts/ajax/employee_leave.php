<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active client datatbale loading
if($get_actionType=="employee_leave_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");

	$table_name='employee_leave';

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
		".$table_name.".id like '%".$searchValue."%' or
		".$table_name.".status like '%".$searchValue."%' or
		employee.name like '%".$searchValue."%'
		) 
		";
	}
	
	if($start_date!='')
	{
		$searchQuery.=" AND DATE(employee_leave.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_leave.status='".$tab_filter."'";
	}

	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".id!=''");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." left join employee on employee_leave.employee_id=employee.id where ".$table_name.".id!='' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
	$obj_brand->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".id!=''  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	//echo $obj_brand->sql; exit;
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;
		$status=$app->utility->get_employee_leave_status(["status"=>$result[$i]["status"]]);
		$detail_btn='<a class="btn btn-xs btn-primary btn-icon mg-r-5 employee_leave_detail_onclick" data-id='.$result[$i]['id'].' href="javascript:void(0)">Detail</a>';
		$option='<div class="btn-toolbar"><div>'.$detail_btn.'</div></div>';
		$leave_start= date('d-m-Y', strtotime($result[$i]['leave_start']));
		$leave_end= date('d-m-Y', strtotime($result[$i]['leave_end']));
		$created_at= date('d-m-Y h:i A', strtotime($result[$i]['created_at']));

		//data
		$data[] = array
		(
			"id"=>$result[$i]["id"],
			"employee_name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_mobile'].' '.$result[$i]['employee_email'].' '.$result[$i]['master_designation_name'],
			"leave_start"=>$leave_start,
			"leave_end"=>$leave_end,
			"status"=>$status['badge'] .'<br/>' .$created_at,	
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
	
echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
?>