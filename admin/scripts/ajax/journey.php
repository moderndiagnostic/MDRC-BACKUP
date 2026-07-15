<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active employee datatbale loading
if($get_actionType=="journey_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");
	
	$table_name='employee_daily_journey';

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
		employee.name like '%".$searchValue."%' or
		employee.lms_employee_code like '%".$searchValue."%' or
		employee_daily_journey.status like '%".$searchValue."%'
		)";
	}

	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_daily_journey.start_datetime) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_daily_journey.status='".$tab_filter."'";
	}
	
	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".status!='Trash'");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(employee_daily_journey.id) as allcount from ".$table_name." left join employee on employee_daily_journey.employee_id=employee.id where ".$table_name.".status!='Trash' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_table = $app->load_model($table_name);
	$obj_table->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
	$obj_table->join_table("employee_detail", "left", array(), array("employee_id"=>"employee_id"));
	$result = $obj_table->execute("SELECT", false, "", "".$table_name.".status!='Trash' ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;
		$detail_btn='<a href="index.php?view=journey_detail&id='.$result[$i]['id'].'" target="_blank" class="btn btn-xs btn-primary btn-icon mg-r-5"><i class="fas fa-info-circle"></i></a>';
		$option='<div class="btn-toolbar"><div>'.$detail_btn.'</div></div>';
		if($result[$i]['status'] == 'Running'){
			$status = '<span class="badge badge-primary">Running</span>';
		} elseif($result[$i]['status'] == 'Pending'){
			$status = '<span class="badge badge-warning">Pending</span>';
		} elseif($result[$i]['status'] == 'Approve By Manager'){
			$status = '<span class="badge badge-success">Approve By Manager</span>';
		} elseif($result[$i]['status'] == 'Approve By Finance'){
			$status = '<span class="badge badge-success">Approve By Finance</span>';
		} elseif($result[$i]['status'] == 'Reject By Manager'){
			$status = '<span class="badge badge-danger">Reject By Manager</span>';
		} elseif($result[$i]['status'] == 'Reject By Finance'){
			$status = '<span class="badge badge-danger">Reject By Finance</span>';
		}
		$amount=$result[$i]['total_km']>0?($result[$i]['total_km']*$result[$i]['employee_detail_per_km']):0;
		//data
		$data[] = array
		(
			"id"=>$result[$i]["id"],
			"employee_name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_lms_employee_code'],
			"start_datetime"=>date('d-m-Y h:i A',strtotime($result[$i]['start_datetime'])),
			"end_datetime"=>!empty($result[$i]['end_datetime']) ? date('d-m-Y h:i A',strtotime($result[$i]['end_datetime'])) : '-',
			"total_km"=>$result[$i]['total_km'].'<br><i class="fas fa-rupee-sign"></i> '.$amount,
			"status"=>$status,
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

//Function for employee addedit
if($actionType=="employeeAddEdit")
{
	$company_name=$app->getPostVar('company_name');
	$status=$app->getPostVar('status');
	$id=$app->getPostVar('id');
	
	if($company_name!='')
	{
		if($id!='')
		{
			$cond=" and id!='".$id."'";
			$update_title='Updated';
		}
		else
		{
			$cond="";
			$update_title='Added';
		}
		
		$upload_dir='employee';

		 //Insert Update Record
		$update_field = array();
		if($_FILES['banner_image']['name']!='') {
		$banner_img11=$app->utility->resize_multi_image($_FILES['banner_image']['name'],$_FILES['banner_image']['tmp_name'],'../../../uploads/'.$upload_dir.'/','800','400','200');	
		$update_field['image']=$banner_img11;
		}
		$update_field['status'] = $status;
		$update_field['added_on'] = date('d-m-Y H:i:s');
		$obj_model_user = $app->load_model("employee");
		$obj_model_user->map_fields($update_field);
		if($id!='')
		{
			$rs=$obj_model_user->execute("UPDATE",false,"","id='".$id."'");
		}
		else
		{
			$rs=$obj_model_user->execute("INSERT",false,"","");
		}
		if($rs>0)
		{
			$msg="employee Record ".$update_title." Successfully.";
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
			$msg='Please Fill Require Data';
			$msgcode=1;
	}
}

//Function for single employee delete
if($actionType=="employeeDelete")
{
	$getid=$app->getPostVar('getid');
	if($getid!= NULL && $getid>0)
	{
		$obj_change_table = $app->load_model('employee');
		$update_id = $obj_change_table->execute("DELETE",false,"","id='".$getid."'");
		
		if($update_id>0)
		{
			$msg='employee Delete Successfully';
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