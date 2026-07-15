<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");

//Function for active client datatbale loading
if($get_actionType=="client_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");

	$table_name='client';

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
		company_name like '%".$searchValue."%' or
		".$table_name.".email like '%".$searchValue."%' or
		".$table_name.".mobile like '%".$searchValue."%' or 	
		".$table_name.".status like '%".$searchValue."%'
		) 
		";
	}
	
	if($start_date!='')
	{
		$searchQuery.=" AND client.created_at BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND client.client_status='".$tab_filter."'";
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
	$obj_brand->join_table("city", "left", array("name"), array("city_id"=>"id"));
	$obj_brand->join_table("state", "left", array("name"), array("state_id"=>"id"));
	$obj_brand->join_table("employee", "left", array("name","lms_employee_code"), array("lms_employee_id"=>"lms_employee_id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".status!='Trash'  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ","id");
	//echo $obj_brand->sql; exit;
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		$sr=$i+1+$row;
		$folder='client';
		$image=$result[$i]["image"];
		$banner_img=$app->utility->get_image_path($image,$folder,"");

		
		$status=$app->utility->get_client_clientStatus(["clientStatus"=>$result[$i]["client_status"],"created_at"=>$result[$i]["created_at"]]);
				
		$detail_btn='<a data-toggle="tooltip" data-placement="top" title="Detail" href="index.php?view=client_detail&id='.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5">Detail</a>';	
		//$edit_btn='<a data-toggle="tooltip" data-placement="top" title="Edit" href="index.php?view=client_addedit&id='.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5"><i class="fas fa-edit"></i></a>';	
		//$delete_btn='<button data-toggle="tooltip" data-placement="top" title="Delete" type="button" class="btn btn-xs btn-danger btn-icon client_delete_onclick" data-id="'.$result[$i]['id'].'" ><i class="fas fa-trash"></i></button>';	
		
		$option='<div class="btn-toolbar"><div>'.$detail_btn.'</div></div>';
		
		$checkbox='<div class="custom-control custom-checkbox"><input type="checkbox" name="del[]" id="del'.$result[$i]['id'].'"  value="'.$result[$i]['id'].'" class="custom-control-input delAll" ><label class="custom-control-label" for="del'.$result[$i]['id'].'"></label></div>';
		
		$employeeDetail=$result[$i]['lms_employee_id']>0?$result[$i]['employee_name'].'<br/>'.$result[$i]['employee_lms_employee_code']:'';
		//data
		$data[] = array
		(
			
			"id"=>$result[$i]["id"],
			"company_name"=>'<b>'.$result[$i]['company_name'].'</b><br/>'.$result[$i]['city_name'].' '.$result[$i]['state_name'],
			"email"=>$result[$i]['email'],
			"phone"=>$result[$i]['mobile'],
			"status"=>$status['badge'].'<br/>'.$status['created_at'],	
			"employee"=>$employeeDetail,
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