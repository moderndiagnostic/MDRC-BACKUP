<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active client datatbale loading
if($get_actionType=="task_office_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");

	$table_name='employee_task_office';

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
		employee.mobile like '%".$searchValue."%' or 	
		) 
		";
	}
	
	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_task_office.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_task_office.status='".$tab_filter."'";
	}

	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where ".$table_name.".status!='Trash'");
	$totalRecords = $result[0]['allcount'];
	
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where ".$table_name.".status!='Trash' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_brand->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".status!='Trash'  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	//echo $obj_brand->sql; exit;
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;
		//$folder='client';
		//$image=$result[$i]["image"];
		//$banner_img=$app->utility->get_image_path($image,$folder,"");

		$status=$app->utility->get_employee_sample_pickup_status(["status"=>$result[$i]["status"]]);
				
		$detail_btn='<a target="_blank" href="https://www.google.com/maps/search/?api=1&query='.$result[$i]['latitude'].','.$result[$i]['longitude'].'" class="btn btn-xs btn-primary btn-icon mg-r-5">Location</a>';
		$detail_btn=$result[$i]['latitude']!=''?$detail_btn:'';	
		$option='<div class="btn-toolbar"><div>'.$detail_btn.'</div></div>';
		
		$time='';
		$time.='Check In : '.date('d-m-Y h:i A', strtotime($result[$i]['check_in'])).'<br/>';
		$time.='Check Out : '.date('d-m-Y h:i A', strtotime($result[$i]['check_out']));
		//data
		$data[] = array
		(
			
			"id"=>$result[$i]["id"],
			"name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_lms_employee_code'],
			"time"=>$time,
			"remark"=>$result[$i]['task_remark'],
			"status"=>$status['badge'],	
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

if($actionType=="export")
{
	// $data=$app->getPostVar('data');
	$app->no_html=true;
	$obj_excel = $app->load_module("PHPExcel");
	$ExeclHeads=array("ID","Employee Name","Check IN","Check Out","Task Remark","Status","Latitude","Longitude","Device Type");
	
	$obj_table = $app->load_model("employee_task_office");
	$obj_table->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
	$result = $obj_table->execute("SELECT", false, "", "employee_task_office.status!='Trash' ","employee_task_office.id desc");
	
	$ucount=1;
	
	foreach($result as $user)
	{
		$check_in=date("d-m-Y H:i:s", strtotime($user['check_in']));
		$check_out=date("d-m-Y H:i:s", strtotime($user['check_out']));
		$data_array[]=array("ID"=>$user['id'],"Employee Name"=>$user['employee_name'],"Check IN"=>$check_in,
		"Check Out"=>$check_out,"Task Remark"=>$user['task_remark'],"Status"=>$user['status'],
		"Latitude"=>$user['latitude'],"Longitude"=>$user['longitude'],"Device Type"=>$user['device_type']);
		$ucount++;
	}
	$filename="TaskofficeList-".date('d-m-Y');
	$sheet=$app->utility->export_excel($ExeclHeads,$data_array,$ExeclHeads,$filename,$ExeclHeads);
		
}
echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
?>