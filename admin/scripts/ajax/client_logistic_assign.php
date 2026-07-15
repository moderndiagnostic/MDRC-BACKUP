<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);
//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");
//Function for active city datatbale loading
if($get_actionType=="client_logistic_assign_list")
{
	$table_name='client_logistic_assign';
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
		employee.name like '%".$searchValue."%' or
		Employee.name like '%".$searchValue."%' or
		client.company_name like '%".$searchValue."%' or
		".$table_name.".request_status like '%".$searchValue."%'
		)
		";
	}
	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where ".$table_name.".request_status!=''");
	$totalRecords = $result[0]['allcount'];
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,client.company_name, employee.name,".$table_name.".* from ".$table_name."
	left join client on ".$table_name.".client_id=client.id
	left join employee on ".$table_name.".employee_id=employee.id
	left join employee as Employee on ".$table_name.".logistic_manager_employee_id=employee.id
	where  ".$table_name.".request_status!='' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_brand->join_table("client", "left", array(), array("client_id"=>"id"));
	$obj_brand->join_table(["client"=>"client","city"=>"city"], "left", array(), array("city_id"=>"id"));
	$obj_brand->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"employee"], "left", array(), array("employee_id"=>"id"));
	$obj_brand->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"Employee"], "left", array(), array("logistic_manager_employee_id"=>"id"));
	$obj_brand->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"EMPLOYEE"], "left", array(), array("assign_by_employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".request_status!=''  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
			$status=$app->utility->get_client_logistic_assign_status(["status"=>$result[$i]["request_status"]]);
			// $edit_btn='<button type="button" class="btn btn-xs btn-primary btn-icon client_logistic_assign_addedit_onclick" data-id="'.$result[$i]['id'].'"><i class="fas fa-edit"></i></button>';
			$detail_btn='<a href="index.php?view=client_logistic_assign_detail&id='.$result[$i]['id'].'" class="btn btn-info btn-primary btn-icon mg-r-5 " target="_blank">Detail</a>';
			$delete_btn='<button type="button" class="btn btn-xs btn-danger btn-icon client_logistic_assign_delete_onclick" data-id="'.$result[$i]['id'].'" ><i class="fas fa-trash"></i></button>';
			$option='<div class="btn-toolbar"><div>'.$edit_btn.' '.$delete_btn.' '.$detail_btn.'</div></div>';
			$checkbox='<div class="custom-control custom-checkbox"><input type="checkbox" name="del[]" id="del'.$result[$i]['id'].'"  value="'.$result[$i]['id'].'" class="custom-control-input delAll" ><label class="custom-control-label" for="del'.$result[$i]['id'].'"></label></div>';
		//data
		$data[] = array
		(
			"checkbox"=>$checkbox,
			"id"=>$result[$i]['id'],
			"client_name"=>$result[$i]['client_company_name'].'<br>'.$result[$i]['city_name'],
			"logistic_manager"=>$result[$i]['Employee_name'],
			"logistic_person"=>$result[$i]['employee_name'],
			"assign_by"=>$result[$i]['EMPLOYEE_name'],
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
//Function for client_logistic_assign addedit
if($actionType=="client_logistic_assignAddEdit")
{
	$name=$app->getPostVar('name');
	$status=$app->getPostVar('status');
	$id=$app->getPostVar('id');
	if($name!='')
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
		$obj_model_check = $app->load_model("client_logistic_assign");
		$rs_check=$obj_model_check->execute("SELECT",false,"","status!='Trash' and name='".$name."' ".$cond."");
		if(count($rs_check)>0)
		{
			$msg='Record Already Exist.';
			$msgcode=1;
			echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
			exit;
		}
		 //Insert Update Record
		$update_field = array();
		$update_field['name'] = $name;
		$update_field['status'] = $status;
		$update_field['created_at'] = date("Y-m-d H:i:s");
		$obj_model_user = $app->load_model("client_logistic_assign");
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
			$msg="Record ".$update_title." Successfully.";
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
//Function for single city delete
if($actionType=="client_logistic_assignDelete")
{
	$getid=$app->getPostVar('getid');
	if($getid!= NULL && $getid>0)
	{
		$obj_change_city = $app->load_model('city');
		$rs_city= $obj_change_city->execute("SELECT",false,"","client_logistic_assign_id='".$getid."' and status!='Trash'");
		if(count($rs_city)==0)
		{
			$update_field = array();
			$update_field['status']= 'Trash';
			$obj_change_table = $app->load_model('client_logistic_assign');
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
			$msg='This client_logistic_assign City Exist.';
			$msgcode=1;
			echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
			exit;
		}
	}
	else
	{
		$msg='Please Try Again.';
		$msgcode=1;
	}
}
//Function for multiple city delete
if($actionType=="client_logistic_assignMultiDelete")
{
	$ids=$app->getPostVar('ids');
	$temp_ids=explode(',',$ids);
	if($ids != NULL && $ids!='')
	{
		for($i=0;$i<count($temp_ids);$i++)
		{
			$update_field = array();
			$update_field['status']= 'Trash';
			$obj_change_table = $app->load_model('client_logistic_assign');
			$obj_change_table->map_fields($update_field);
			$update_id = $obj_change_table->execute("UPDATE",false,"","id='".$temp_ids[$i]."'");
		}
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