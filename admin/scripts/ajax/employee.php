<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active employee datatbale loading
if($get_actionType=="employee_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");
	$designation_id=$app->getPostVar("designation_id");

	$table_name='employee';

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
		employee.email like '%".$searchValue."%' or
		employee.lms_employee_code like '%".$searchValue."%' or
		employee.mobile like '%".$searchValue."%'
		) 
		";
	}

	if($start_date!='')
	{
		$searchQuery.=" AND date(employee.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if(!empty($designation_id))
	{
		$searchQuery.=" AND employee.master_designation_id='".$designation_id."'";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		if($tab_filter=='All In LMS'){
			$searchQuery.=" AND employee.lms_employee_id!=''"; 
		} else {
			$searchQuery.=" AND employee.lms_employee_id='0'"; 
		}
	}
	$groupBy="employee.id"; 

	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".status!='Trash'");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".status!='Trash' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_table->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
	$obj_table->join_table("city", "left", array(), array("city_id"=>"id"));
	$obj_table->join_table(["employee"=>"employee_r"], "left", array(), array("reporting_employee_lms_id"=>"lms_employee_id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".status!='Trash' ".$searchQuery."",$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage,$groupBy);
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;
		//$folder='employee';
		//$image=$result[$i]["image"];
		//$banner_img=$app->utility->get_image_path($image,$folder,"");

		$status='<img src="assets/img/status/'.$result[$i]['status'].'.png" onclick="javascript:change_status(\''.$result[$i]['id'].'\', \'employee\', \''.$result[$i]['status'].'\')" border="0" id="status_'.$result[$i]['id'].'" style="cursor:pointer" alt="'.$result[$i]['status'].'" data-toggle="tooltip" data-placement="top" title="Status" title="'.$result[$i]['status'].'" />';
		
		$detail_btn='<a data-toggle="tooltip" data-placement="top" title="Detail" href="index.php?view=employee_detail&id='.$result[$i]['id'].'" class="btn btn-xs btn-warning btn-icon mg-r-5"><i class="fas fa-play"></i></a>';	
		$edit_btn='<a data-toggle="tooltip" data-placement="top" title="Edit" href="javascript:void(0)" data-id="'.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5 employee_addedit_onclick"><i class="fas fa-edit"></i></a>';	
		//$delete_btn='<button data-toggle="tooltip" data-placement="top" title="Delete" type="button" class="btn btn-xs btn-danger btn-icon employee_delete_onclick" data-id="'.$result[$i]['id'].'" ><i class="fas fa-trash"></i></button>';	
		
		$option='<div class="btn-toolbar"><div>'.$detail_btn.' '.$edit_btn.''.$delete_btn.'</div></div>';
		
		$reporting=$result[$i]['reporting_employee_lms_id']>0?'<b>'.$result[$i]['employee_r_name'].'</b><br/>'.$result[$i]['employee_r_lms_employee_code']:'';
		
		//data
		$data[] = array
		(
			"id"=>$result[$i]["id"],
			"name"=>'<b>'.$result[$i]['name'].'</b><br/>'.$result[$i]['master_designation_name'].'<br/>'.$result[$i]['lms_employee_code'],
			"email"=>$result[$i]['email'].'<br/>'.$result[$i]['mobile'],
			"reporting"=>$reporting,
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
	$id=$app->getPostVar('id');
	$area=$app->getPostVar('area');
	$name=$app->getPostVar('name');
	$mobile=$app->getPostVar('mobile');
	$email=$app->getPostVar('email');
	$lms_employee_code=$app->getPostVar('lms_employee_code');

	if($name=='' || $lms_employee_code=='')
	{
		$msg="Please Fill Required Data.";
		$msgcode=1;
		echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
		exit;
	}
	
	$cond="";
	if($email!='' && $mobile!='') {
		$cond.=" and (email='".$email."' or mobile='".$mobile."' or lms_employee_code='".$lms_employee_code."')";
	} else if($email!='' && $mobile==''){
		$cond.=" and (email='".$email."' or lms_employee_code='".$lms_employee_code."')";
	} else if($email=='' && $mobile!=''){
		$cond.=" and (mobile='".$mobile."' or lms_employee_code='".$lms_employee_code."')";
	} else {
		$cond.=" and (lms_employee_code='".$lms_employee_code."')";
	}
	if($id!='') {
		$cond.=" and id!='".$id."'";
	}
	
	//check for duplicate
	$obj_model_user = $app->load_model("employee");
	$employee=$obj_model_user->execute("SELECT",false,"","id!=0 ".$cond);
	if(count($employee)>0) {
		$msg="Mobile or Email or lms employee code already exist.";
		$msgcode=1;
		echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
		exit;
	}
	

	$update_field = array();

	if($_FILES['imageFile']['name']!='') {
		$image=$app->utility->resize_single_image($_FILES['imageFile']['name'],$_FILES['imageFile']['tmp_name'],'../../uploads/employee/','800');	
		$update_field['image'] = $image;
	}
		
	if($id!='') {
		//update
		
		$update_field['updated_at'] = date('Y-m-d H:i:s');
		$obj_model_user = $app->load_model("employee");
		$obj_model_user->map_fields($update_field);
		$obj_model_user->execute("UPDATE",false,"","id='".$id."'");

		$update_field = array();
		$update_field['updated_at'] = date('Y-m-d H:i:s');
		$obj_model_user = $app->load_model("employee_detail");
		$obj_model_user->map_fields($update_field);
		$obj_model_user->execute("UPDATE",false,"","employee_id='".$id."'");


		$msg="Employee Record Updated Successfully.";
		$msgcode=0;

	} else {
		
		$update_field['employee_role'] = 'Admin';
		$update_field['created_at'] = date('Y-m-d H:i:s');
		$update_field['updated_at'] = date('Y-m-d H:i:s');
		$obj_model_user = $app->load_model("employee");
		$obj_model_user->map_fields($update_field);
		$ins=$obj_model_user->execute("INSERT",false,"","");

		$update_field = array();
		$update_field['area'] = $area;
		$update_field['employee_id'] = $ins;
		$update_field['updated_at'] = date('Y-m-d H:i:s');
		$obj_model_user = $app->load_model("employee_detail");
		$obj_model_user->map_fields($update_field);
		$obj_model_user->execute("INSERT",false,"","");
		
		$msg="Employee Record Insert Successfully.";
		$msgcode=0;
	}
}
	
echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
?>