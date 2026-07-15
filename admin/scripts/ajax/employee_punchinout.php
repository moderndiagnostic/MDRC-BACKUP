<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active client datatbale loading
if($get_actionType=="employee_punchinout_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");

	$table_name='employee_punch_inout';

	## Read value
	$draw = $app->getPostVar('draw');
	$row = $app->getPostVar('start');
	$rowperpage = $app->getPostVar('length'); // Rows display per page
	$orderArray = $app->getPostVar('order');
	$columnIndex = $orderArray[0]['column']; // Column index
	
	$columnArray = $app->getPostVar('columns');
	$columnName = $columnArray[$columnIndex]['data']; // Column name
	
	if($columnName=='in' || $columnName=='out' || $columnName=='btn' || $columnName=='image')
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
		employee.email like '%".$searchValue."%' or
		employee.mobile like '%".$searchValue."%' or 	
		employee.name like '%".$searchValue."%' or
		employee.lms_employee_code like '%".$searchValue."%'
		) 
		";
	}
	
	if($start_date!='')
	{
		$searchQuery.=" AND employee_punch_inout.punch_date BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_punch_inout.punchout_datetime is NULL";
	}

	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where ".$table_name.".id!='' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".id!='' ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	//echo $obj_brand->sql; exit;
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;
		$folder='punch';
		$image=$result[$i]["employee_photo"];
		$image=$app->utility->get_image_path($image,$folder,"large");

		$detail_btn='<a data-toggle="tooltip" data-placement="top" title="Detail" target="_blank" href="https://www.google.com/maps/search/?api=1&query='.$result[$i]['latitude'].','.$result[$i]['longitude'].'" class="btn btn-xs btn-primary btn-icon mg-r-5">Location</a>';	
		$detail_btn=$result[$i]['latitude']!=''?$detail_btn:'';
		$option='<div class="btn-toolbar"><div>'.$detail_btn.'</div></div>';
	
		$employeeDetail=$result[$i]['lms_employee_id']>0?$result[$i]['employee_name'].'<br/>'.$result[$i]['employee_lms_employee_code']:'';
		//data
		$data[] = array
		(
			
			"id"=>$result[$i]["id"],
			"name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['lms_employee_code'],
			"mobile"=>$result[$i]['employee_email'].'<br/>'.$result[$i]['employee_mobile'],
			"in"=>date('d-m-Y h:i A', strtotime($result[$i]['punchin_datetime'])),
			"out"=>$result[$i]['punchout_datetime']!=''?date('d-m-Y h:i A', strtotime($result[$i]['punchout_datetime'])):'',	
			"image"=>'<a href="'.$image.'" class="image-popup"><img src="'.$image.'" style="width:80px;"/></a>',
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

//Function for client addedit
if($actionType=="clientAddEdit")
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
		
		$upload_dir='client';

		 //Insert Update Record
		$update_field = array();
		if($_FILES['banner_image']['name']!='') {
		$banner_img11=$app->utility->resize_multi_image($_FILES['banner_image']['name'],$_FILES['banner_image']['tmp_name'],'../../../uploads/'.$upload_dir.'/','800','400','200');	
		$update_field['image']=$banner_img11;
		}
		$update_field['status'] = $status;
		$update_field['added_on'] = date('d-m-Y H:i:s');
		$obj_model_user = $app->load_model("client");
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
			$msg="client Record ".$update_title." Successfully.";
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

//Function for single client delete
if($actionType=="clientDelete")
{
	$getid=$app->getPostVar('getid');
	
	if($getid!= NULL && $getid>0)
	{
		$obj_change_table = $app->load_model('client');
		$update_id = $obj_change_table->execute("DELETE",false,"","id='".$getid."'");
		
		if($update_id>0)
		{
			$msg='client Delete Successfully';
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


//Function for multiple client delete
if($actionType=="clientMultiDelete")
{
	$ids=$app->getPostVar('ids');
	
	if($ids != NULL && $ids!='')
	{
		
		$obj_change_table = $app->load_model('client');
		$update_id = $obj_change_table->execute("DELETE",false,"","id IN (".$ids.")");
		
		if($update_id>0)
		{
			$msg='client Delete Successfully';
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