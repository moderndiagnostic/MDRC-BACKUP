<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active employee datatbale loading
if($get_actionType=="task_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");
	
	$table_name='employee_task_master';

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
		client.company_name like '%".$searchValue."%' or 	
		employee_task_master.purpose like '%".$searchValue."%' or
		employee_task_master.status like '%".$searchValue."%'
		)";
	}

	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_task_master.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_task_master.status='".$tab_filter."'";
	}
	
	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".status!='Trash'");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(employee_task_master.id) as allcount from ".$table_name." LEFT JOIN client on client.id=employee_task_master.client_id LEFT JOIN employee on employee.id=employee_task_master.employee_primary_id where ".$table_name.".status!='Trash' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_table = $app->load_model($table_name);
	$obj_table->join_table("client", "left", array("company_name","mobile"), array("client_id"=>"id"));
	$obj_table->join_table("employee", "left", array("name","lms_employee_code"), array("employee_primary_id"=>"id"));
	$result = $obj_table->execute("SELECT", false, "", "".$table_name.".status!='Trash' ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;

		$banner_img='';
		$obj_table = $app->load_model("employee_task_master_update");
		$checkIN = $obj_table->execute("SELECT", false, "", "activity='Check In' and employee_task_master_id='".$result[$i]["id"]."'");
		if(count($checkIN)>0){
			$banner_img=$app->utility->get_image_path($checkIN[0]['checkin_photo'],'taskUpdate',"large");
		}

		//$folder='employee';
		//$image=$result[$i]["image"];
		//$banner_img=$app->utility->get_image_path($image,$folder,"");

		$status=$app->utility->getTaskStatusHtml($result[$i]["status"]);
				
		$detail_btn='<a href="index.php?view=task_detail&id='.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5">Detail</a>';	
		//$edit_btn='<a data-toggle="tooltip" data-placement="top" title="Edit" href="javascript:void(0)" data-id="'.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5 employee_addedit_onclick"><i class="fas fa-edit"></i></a>';	
		//$delete_btn='<button data-toggle="tooltip" data-placement="top" title="Delete" type="button" class="btn btn-xs btn-danger btn-icon employee_delete_onclick" data-id="'.$result[$i]['id'].'" ><i class="fas fa-trash"></i></button>';	
		$option='<div class="btn-toolbar"><div>'.$detail_btn.' '.$edit_btn.''.$delete_btn.'</div></div>';

		$created_at= date('d-m-Y h:i A', strtotime($result[$i]['created_at']));
			
		//data
		$data[] = array
		(
			"id"=>$result[$i]["id"],
			"client"=>'<b>'.$result[$i]['client_company_name'].'</b><br/>'.$result[$i]['client_mobile'],
			"employee"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_lms_employee_code'],
			"purpose"=>$result[$i]['purpose'],
			"status"=>$status,	
			"created_at"=>$created_at,	
			"btn"=>$option,
			"image"=>'<a href="'.$banner_img.'" class="image-popup"><img src="'.$banner_img.'" class="wd-100" /></a>'
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