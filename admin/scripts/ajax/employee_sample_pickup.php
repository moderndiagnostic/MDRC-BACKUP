<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active client datatbale loading
if($get_actionType=="employee_sample_pickup_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");

	$table_name='employee_sample_pickup';

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
		employee_sample_pickup.pickup_date like '%".$searchValue."%' or
		employee_sample_pickup.status like '%".$searchValue."%' or
		employee.email like '%".$searchValue."%' or
		employee.mobile like '%".$searchValue."%' or 	
		employee.lms_employee_code like '%".$searchValue."%' or 	
		employee.name like '%".$searchValue."%' or 	
		client.company_name like '%".$searchValue."%' or 
		client.phone like '%".$searchValue."%' or 	
		client.mobile like '%".$searchValue."%' or 	
		".$table_name.".status like '%".$searchValue."%'
		) 
		";
	}
	
	if($start_date!='')
	{
		$searchQuery.=" AND DATE(employee_sample_pickup.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_sample_pickup.status='".$tab_filter."'";
	}

	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".status!='Trash'");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." left join employee on employee.id=".$table_name.".employee_id left join client on client.id=".$table_name.".client_id where ".$table_name.".status!='Trash' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_brand->join_table("client", "left", array("company_name","email","mobile"), array("client_id"=>"id"));
	$obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
	$obj_brand->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".status!='Trash'  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	//echo $obj_brand->sql; exit;
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;

		$banner_img='';
		$obj_table = $app->load_model("employee_sample_pickup_update");
		$checkIN = $obj_table->execute("SELECT", false, "", "pickup_status='Check In' and employee_sample_pickup_id='".$result[$i]["id"]."'");
		if(count($checkIN)>0){
			$banner_img=$app->utility->get_image_path($checkIN[0]['checkin_photo'],'samplePickupUpdate/'.$checkIN[0]['employee_id'],"large");
		}

		$status=$app->utility->get_employee_sample_pickup_status(["status"=>$result[$i]["status"]]);
				
		$detail_btn='<a href="index.php?view=employee_sample_pickup_detail&id='.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5" target="_blank">Detail</a>';	
		$option='<div class="btn-toolbar"><div>'.$detail_btn.'</div></div>';

		$summary='Collect Sample : <b>'.$result[$i]['collect_sample'].'</b>';
		$summary.='<br/>Collect Payment : <b>'.$result[$i]['collect_payment'].'</b>';
	
		//data
		$data[] = array
		(
			
			"id"=>$result[$i]["id"],
			"employee_name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_mobile'].' '.$result[$i]['employee_email'].' '.$result[$i]['master_designation_name'],
			"client_company_name"=>'<b>'.$result[$i]['client_company_name'].'</b><br/>'.$result[$i]['client_email'].' '.$result[$i]['client_mobile'],
			"pickup_date"=>$result[$i]['pickup_date'],
			"summary"=>$summary,
			"status"=>$status['badge'],	
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