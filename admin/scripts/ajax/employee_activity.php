<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");
//Function for active city datatbale loading

if($get_actionType=="employee_activity_list")
{
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$tab_filter=$app->getPostVar("tab_filter");
	$table_name='employee_activity';
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
	if($_SESSION['employeeRole']!='Admin')
	{
		$searchQuery=' AND employee_activity.employee_id="'.$_SESSION['employeeId'].'"';
	}
	else
	{
		$searchQuery='';
	}
	if($start_date!='')
	{
		$searchQuery.="  AND date(employee_activity.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_activity.employee_activity_type='".$tab_filter."'";
	}
	$searchArray=$app->getPostVar('search');
	$searchValue = $searchArray['value']; // Search value

	if($searchValue != '')
	{
		$searchQuery = " and (
		".$table_name.".id like '%".$searchValue."%' or
		".$table_name.".title like '%".$searchValue."%' or
		".$table_name.".activity_desc like '%".$searchValue."%' or
		".$table_name.".ip like '%".$searchValue."%' or
		employee.name like '%".$searchValue."%' 

		)
		";
	}
	
			## Total number of records without filtering
			$obj_table = $app->load_model($table_name);
			$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name."  where ".$table_name.".id!='0'");
			$totalRecords = $result[0]['allcount'];

			## Total number of records with filtering
			$obj_table = $app->load_model($table_name);
			$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." left join employee on employee_activity.employee_id=employee.id where  ".$table_name.".id!='0' ".$searchQuery);
			$totalRecordwithFilter = $result[0]['allcount'];

			## Fetch records
			$obj_brand = $app->load_model($table_name);
			$obj_brand->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".id!='0'  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
			$data = array();
			for($i=0;$i<count($result);$i++)
			{
				$delete_btn='<button type="button" class="btn btn-xs btn-danger btn-icon employee_activity_delete_onclick" data-id="'.$result[$i]['id'].'" ><i class="fas fa-trash"></i></button>';
				$option='<div class="btn-toolbar"><div>'.$delete_btn.'</div></div>';
				$checkbox='<div class="custom-control custom-checkbox"><input type="checkbox" name="del[]" id="del'.$result[$i]['id'].'"  value="'.$result[$i]['id'].'" class="custom-control-input delAll" ><label class="custom-control-label" for="del'.$result[$i]['id'].'"></label></div>';
				$updated_at= date('d-m-Y h:i A', strtotime($result[$i]['updated_at']));
				//data
				$data[] = array
				(
					"checkbox"=>$checkbox,
					"id"=>$result[$i]["id"],
					"employee_name"=>'<b>'.$result[$i]['employee_name'],
					"title"=>$result[$i]['title'],
					"ip"=>$result[$i]['ip'],
					"updated_at"=>$updated_at,
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

// -----------------MULTY=DELETE---------------

if($actionType=="employee_activityMultiDelete")
{
	$ids=$app->getPostVar('ids');
	$temp_ids=explode(',',$ids);
	if($ids != NULL && $ids!='')
	{
		for($i=0;$i<count($temp_ids);$i++)
		{
			$update_field = array();
			$obj_change_table = $app->load_model('employee_activity');
			$obj_change_table->map_fields($update_field);
			$obj_change_table->execute("DELETE",false,"","id='".$temp_ids[$i]."'");
  		}

		if($app>0)
		{
			$msg='Sucess';
			$msgcode=0;
		}
		else
		{
			$msg='Please Again.';
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