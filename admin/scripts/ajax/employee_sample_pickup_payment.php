<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);
//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");
//Function for active city datatbale loading
if($get_actionType=="payment_list")
{
	$table_name='employee_sample_pickup_payment';
	## Read value
	$start_date=$app->getPostVar("start_date");
	$end_date=$app->getPostVar("end_date");
	$employee_id=$app->getPostVar("employee_id");
	$tab_filter=$app->getPostVar("tab_filter");

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
	if($start_date!='')
	{
		$searchQuery.=" AND DATE(employee_sample_pickup_payment.transaction_date) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	if($employee_id!='' && $employee_id!=0)
	{
		$searchQuery.=" AND employee_sample_pickup_payment.employee_id='".$employee_id."'";
	}
	if($tab_filter!='' && $tab_filter!='All')
	{
		$searchQuery.=" AND employee_sample_pickup_payment.payment_status='".$tab_filter."'";
	}

	if($searchValue != '')
	{
		$searchQuery = " and (
		".$table_name.".id like '%".$searchValue."%' or
		employee.name like '%".$searchValue."%' or
		client.company_name like '%".$searchValue."%' or
		".$table_name.".amount like '%".$searchValue."%' or
		".$table_name.".payment_status like '%".$searchValue."%'
		)
		";
	}
	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name."  where ".$table_name.".id!='0'");
	$totalRecords = $result[0]['allcount'];
	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount,".$table_name.".* from ".$table_name." left join client on ".$table_name.".client_id=client.id left join employee on ".$table_name.".employee_id=employee.id where ".$table_name.".id!='0' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];
	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_brand->join_table("client", "left", array(), array("client_id"=>"id"));
	$obj_brand->join_table(["client"=>"client","city"=>"city"], "left", array("name"), array("city_id"=>"id"));
	$obj_brand->join_table(["client"=>"client","state"=>"state"], "left", array("name"), array("state_id"=>"id"));
	$obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
	$obj_brand->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".id!='0'  ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		if($result[$i]['payment_status']=='Success')
		{
			$status='<span class="badge badge-success ml-1">Success</span>';
		}
		else if($result[$i]['payment_status']=='Fail')
		{
			$status='<span class="badge badge-danger ml-1">Fail</span>';
		}
		else
		{
			$status='<span class="badge badge-warning ml-1">Pending</span>';
		}
			
			$edit_btn='<button type="button" class="btn btn-xs btn-primary btn-icon payment_link_addedit_onclick" data-id="'.$result[$i]['id'].'"><i class="fas fa-edit"></i></button>';
			$view_btn='<button type="button" class="btn btn-xs btn-warning btn-icon payment_link_detail_onclick" data-id="'.$result[$i]['id'].'"><i class="fas fa-eye"></i></button>';
			$delete_btn='<button type="button" class="btn btn-xs btn-danger btn-icon payment_link_delete_onclick" data-id="'.$result[$i]['id'].'" ><i class="fas fa-trash"></i></button>';
			$option='<div class="btn-toolbar"><div>'.$view_btn.'</div></div>';
			$checkbox='<div class="custom-control custom-checkbox"><input type="checkbox" name="del[]" id="del'.$result[$i]['id'].'"  value="'.$result[$i]['id'].'" class="custom-control-input delAll" ><label class="custom-control-label" for="del'.$result[$i]['id'].'"></label></div>';
		//data
		$data[] = array
		(
			"id"=>$result[$i]['id'],
			"client_name"=>!empty($result[$i]['client_mobile'])?$result[$i]['client_company_name'].'<br>'.$result[$i]['client_mobile'].'<br/>'.$result[$i]['city_name'].' '.$result[$i]['state_name']:$result[$i]['client_company_name'],
			"employee_name"=>$result[$i]['employee_name'].'<br>'.$result[$i]['master_designation_name'].'<br>'.$result[$i]['employee_lms_employee_code'],
			"amount"=>$app->utility->moneyFormatIndia($result[$i]['amount']),
			"transaction_id" => '<b>PAYU : </b>'.$result[$i]['transaction_id'] . '<br/>' . (!empty($result[$i]['lis_transaction_id']) ? '<b>LIS : </b>' . $result[$i]['lis_transaction_id'] : ''),
			"payment_status"=>$status . '<br/>' . (!empty($result[$i]['transaction_date']) ? date('d-m-Y h:i A', strtotime($result[$i]['transaction_date'])) : ''),
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

if($actionType=="payment_linkDelete")
{
	$getid=$app->getPostVar('getid');
	if($getid!= NULL && $getid>0)
	{
		$obj_change_city = $app->load_model('city');
		$rs_city= $obj_change_city->execute("SELECT",false,"","payment_link_id='".$getid."' and status!='Trash'");
		if(count($rs_city)==0)
		{
			$update_field = array();
			$update_field['status']= 'Trash';
			$obj_change_table = $app->load_model('payment_link');
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
			$msg='This payment_link City Exist.';
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
if($actionType=="payment_linkMultiDelete")
{
	$ids=$app->getPostVar('ids');
	$temp_ids=explode(',',$ids);
	if($ids != NULL && $ids!='')
	{
		for($i=0;$i<count($temp_ids);$i++)
		{
			$update_field = array();
			$update_field['status']= 'Trash';
			$obj_change_table = $app->load_model('payment_link');
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