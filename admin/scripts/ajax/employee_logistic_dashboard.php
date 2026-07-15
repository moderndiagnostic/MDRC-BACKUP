<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action
$get_actionType=$app->getGetVar("actionType");
$actionType=$app->getPostVar("actionType");
$selected_employee=$app->getPostVar("selected_employee");
$selected_employee_name=$app->getPostVar("selected_employee_name");

$start_date=$app->getPostVar("start_date");
$end_date=$app->getPostVar("end_date");
$noTaskHtml='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
				<div class="card p-3 rounded-3 border-0 hover-shadow">
					<div class="text-center">
					<h6 class="text-uppercase text-muted text-xs mb-2">
						Total Logistics Visits
					</h6>
					<div class="d-flex justify-content-center align-items-center">
						<h3 class="mb-0">0</h3>
					</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
				<div class="card p-3 rounded-3 border-0 hover-shadow">
					<div class="text-center">
					<h6 class="text-uppercase text-muted text-xs mb-2">
						Total Collect Sample
					</h6>
					<div class="d-flex justify-content-center align-items-center">
						<h3 class="mb-0">0</h3>
					</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
				<div class="card p-3 rounded-3 border-0 hover-shadow">
					<div class="text-center">
					<h6 class="text-uppercase text-muted text-xs mb-2">
						Total Collect Payment
					</h6>
					<div class="d-flex justify-content-center align-items-center">
						<h3 class="mb-0">0</h3>
					</div>
					</div>
				</div>
			</div>';

if($actionType=="getData")
{
	$employeeId=$_SESSION['employeeId'];
	//$employeeId=4;

	$obj_table_tble= $app->load_model('employee');
	$employee= $obj_table_tble->execute("SELECT", false, "","id='".$employeeId."'");
	
	$subEmployee=$app->utility->getSubEmployee($employee[0]['lms_employee_id']);
	$subEmployeeId=array_column($subEmployee,'id');
	$searchQuery='';
	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_sample_pickup.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}

	$taskDataSub=[];
	if(count($subEmployeeId)>0)
	{
		$obj_table_tble= $app->load_model('employee_sample_pickup');
		$visitCountDataSub= $obj_table_tble->execute("SELECT", false, "SELECT COUNT(*) AS visit_count,COUNT(CASE WHEN collect_sample = 'Yes' THEN 1 END) AS collect_sample_count,COUNT(CASE WHEN collect_payment = 'Yes' THEN 1 END) AS collect_payment_count  FROM employee_sample_pickup WHERE employee_id IN (".implode(',',$subEmployeeId).") ".$searchQuery."");

		$visitDataSub= $obj_table_tble->execute("SELECT", false, "SELECT 
        employee_sample_pickup.client_id, 
        c.company_name AS client_name, 
        COUNT(*) AS visit_count 
		FROM employee_sample_pickup 
		LEFT JOIN client c ON employee_sample_pickup.client_id = c.id
		WHERE employee_sample_pickup.employee_id IN (".implode(',',$subEmployeeId).") ".$searchQuery."
		GROUP BY employee_sample_pickup.client_id");
	}
	
	$chartLabel2=['Total Logistics Visits'=>$visitCountDataSub[0]['visit_count'],'Total Collect Sample'=>$visitCountDataSub[0]['collect_sample_count'],'Total Collect Payment'=>$visitCountDataSub[0]['collect_payment_count']];
	$chartColor=[generateRandomColor(),generateRandomColor(),generateRandomColor()];

	$chartData2=[
		'chartLabel'=>array_keys($chartLabel2),
		'chartValue'=>array_values($chartLabel2),
		'chartColor'=>$chartColor
	];

	$html2='';
	if(count($visitDataSub)>0)
	{
		$html2.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
				<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',$subEmployeeId).'" data-name="Team" data-purpose="Total Logistics Visits">
					<div class="card p-3 rounded-3 border-0 hover-shadow">
						<div class="text-center">
						<h6 class="text-uppercase text-muted text-xs mb-2">
							Total Logistics Visits
						</h6>
						<div class="d-flex justify-content-center align-items-center">
							<h3 class="mb-0"> '. $visitCountDataSub[0]['visit_count'] .'</h3>
						</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
				<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',$subEmployeeId).'" data-name="Team" data-purpose="Total Collect Sample">
					<div class="card p-3 rounded-3 border-0 hover-shadow">
						<div class="text-center">
						<h6 class="text-uppercase text-muted text-xs mb-2">
							Total Collect Sample
						</h6>
						<div class="d-flex justify-content-center align-items-center">
							<h3 class="mb-0"> '. $visitCountDataSub[0]['collect_sample_count'] .'</h3>
						</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
				<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',$subEmployeeId).'" data-name="Team" data-purpose="Total Collect Payment">
					<div class="card p-3 rounded-3 border-0 hover-shadow">
						<div class="text-center">
						<h6 class="text-uppercase text-muted text-xs mb-2">
							Total Collect Payment
						</h6>
						<div class="d-flex justify-content-center align-items-center">
							<h3 class="mb-0"> '. $visitCountDataSub[0]['collect_payment_count'] .'</h3>
						</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-sm-12 col-md-12 col-lg-12 mb-3">
				<div class="card df-example demo-table p-4">
					<table class="table table-hover dataTable">
						<thead class="table-dark">
							<tr>
								<th class="wd-10p">SR No.</th>
								<th class="wd-30p">Client Name</th>
								<th class="wd-10p">Visit</th>
							</tr>
						</thead>
						<tbody>';
						foreach($visitDataSub as $key=>$item){
							$html2.='<tr>
								<td>'.($key+1).'</td>
								<td>'.$item['client_name'].'</td>
								<td>'.$item['visit_count'].'</td>
							</tr>';
						}
						$html2.='</tbody>
					</table>
				</div>
			</div>';
		
	}
	else
	{
		$html2.=$noTaskHtml;
	}
	
	$obj_table_tble= $app->load_model('employee_sample_pickup');
	$visitCountData= $obj_table_tble->execute("SELECT", false, "SELECT COUNT(*) AS visit_count,COUNT(CASE WHEN collect_sample = 'Yes' THEN 1 END) AS collect_sample_count,COUNT(CASE WHEN collect_payment = 'Yes' THEN 1 END) AS collect_payment_count  FROM employee_sample_pickup WHERE employee_id = '".$employeeId."' ".$searchQuery."");
				
	$visitData= $obj_table_tble->execute("SELECT", false, "SELECT 
        employee_sample_pickup.client_id, 
        c.company_name AS client_name, 
        COUNT(*) AS visit_count 
    FROM employee_sample_pickup 
    LEFT JOIN client c ON employee_sample_pickup.client_id = c.id
    WHERE employee_sample_pickup.employee_id = '".$employeeId."' ".$searchQuery."
    GROUP BY employee_sample_pickup.client_id");
	
	$chartLabel=['Total Logistics Visits'=>$visitCountData[0]['visit_count'],'Total Collect Sample'=>$visitCountData[0]['collect_sample_count'],'Total Collect Payment'=>$visitCountData[0]['collect_payment_count']];
	$chartColor=[generateRandomColor(),generateRandomColor(),generateRandomColor()];
	
	$chartData1=[
		'chartLabel'=>array_keys($chartLabel),
		'chartValue'=>array_values($chartLabel),
		'chartColor'=>$chartColor
	];

	$html1='';
	if(count($visitData)>0)
	{
		
		$html1.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-name="'.$employee[0]['name'].'" data-purpose="Total Logistics Visits">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Logistics Visits
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. $visitCountData[0]['visit_count'] .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-name="'.$employee[0]['name'].'" data-purpose="Total Collect Sample">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Collect Sample
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. $visitCountData[0]['collect_sample_count'] .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-name="'.$employee[0]['name'].'" data-purpose="Total Collect Payment">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Collect Payment
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. $visitCountData[0]['collect_payment_count'] .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12 mb-3">
					<div class="card df-example demo-table p-4">
						<table class="table table-hover table-bordered dataTable">
							<thead class="table-dark">
								<tr>
									<th class="wd-10p">SR No.</th>
									<th class="wd-30p">Client Name</th>
									<th class="wd-10p">Visit</th>
								</tr>
							</thead>
							<tbody>';
							foreach($visitData as $key=>$item){
								$html1.='<tr>
									<td>'.($key+1).'</td>
									<td>'.$item['client_name'].'</td>
									<td>'.$item['visit_count'].'</td>
								</tr>';
							}
							$html1.='</tbody>
						</table>
					</div>
				</div>';
		
	}
	else
	{
		$html1.=$noTaskHtml;
	}

	$html3='';
	if(count($visitCountData) || count($visitCountDataSub)>0)
	{
			$html3.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',array_merge($subEmployeeId,[$employeeId])).'" data-name="All Team" data-purpose="Total Logistics Visits">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Logistics Visits
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. ($visitCountData[0]['visit_count']+$visitCountDataSub[0]['visit_count']) .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',array_merge($subEmployeeId,[$employeeId])).'" data-name="All Team" data-purpose="Total Collect Sample">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Collect Sample
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. ($visitCountData[0]['collect_sample_count']+$visitCountDataSub[0]['collect_sample_count']) .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',array_merge($subEmployeeId,[$employeeId])).'" data-name="All Team" data-purpose="Total Collect Payment">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Collect Payment
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. ($visitCountData[0]['collect_payment_count']+$visitCountDataSub[0]['collect_payment_count']) .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>';
	}
	else
	{
		$html3.=$noTaskHtml;
	}

	$msg="Employee Record Get Successfully.";
	$msgcode=0;

	## Response
	$response = array("RESULT"=>$msgcode,"msg"=>$msg,"chartData1"=>$chartData1,"chartData2"=>$chartData2,"html1"=>$html1,"html2"=>$html2,"html3"=>$html3);
		
	echo json_encode($response);
	exit;
}
if($actionType=="getDataSub")
{
	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_sample_pickup .created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	$html4='';
	if(!empty($selected_employee))
	{
		$obj_table_tble= $app->load_model('employee');
		$employee= $obj_table_tble->execute("SELECT", false, "","id='".$selected_employee."'");

		$obj_table_tble= $app->load_model('employee_sample_pickup');
		$visitCountData2= $obj_table_tble->execute("SELECT", false, "SELECT COUNT(*) AS visit_count,COUNT(CASE WHEN collect_sample = 'Yes' THEN 1 END) AS collect_sample_count,COUNT(CASE WHEN collect_payment = 'Yes' THEN 1 END) AS collect_payment_count  FROM employee_sample_pickup WHERE employee_id = '".$selected_employee."' ".$searchQuery."");
					
		$visitData2= $obj_table_tble->execute("SELECT", false, "SELECT 
			employee_sample_pickup.client_id, 
			c.company_name AS client_name, 
			COUNT(*) AS visit_count 
		FROM employee_sample_pickup 
		LEFT JOIN client c ON employee_sample_pickup.client_id = c.id
		WHERE employee_sample_pickup.employee_id = '".$employeeId."' ".$searchQuery."
		GROUP BY employee_sample_pickup.client_id");

		$chartLabel=['Total Logistics Visits'=>$visitCountData[0]['visit_count'],'Total Collect Sample'=>$visitCountData[0]['collect_sample_count'],'Total Collect Payment'=>$visitCountData[0]['collect_payment_count']];
		$chartColor=[generateRandomColor(),generateRandomColor(),generateRandomColor()];

		$chartData3=[
			'chartLabel'=>array_keys($chartLabel),
			'chartValue'=>array_values($chartLabel),
			'chartColor'=>$chartColor
		];

		if(count($taskData2)>0)
		{
			$html4.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-name="'.$employee[0]['name'].'">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Logistics Visits
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. $visitCountData2[0]['visit_count'] .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-name="'.$employee[0]['name'].'">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Collect Sample
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. $visitCountData2[0]['collect_sample_count'] .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
					<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-name="'.$employee[0]['name'].'">
						<div class="card p-3 rounded-3 border-0 hover-shadow">
							<div class="text-center">
							<h6 class="text-uppercase text-muted text-xs mb-2">
								Total Collect Payment
							</h6>
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="mb-0"> '. $visitCountData2[0]['collect_payment_count'] .'</h3>
							</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12 mb-3">
					<div class="card df-example demo-table p-4">
						<table class="table table-hover table-bordered dataTable">
							<thead class="table-dark">
								<tr>
									<th class="wd-10p">SR No.</th>
									<th class="wd-30p">Client Name</th>
									<th class="wd-10p">Visit</th>
								</tr>
							</thead>
							<tbody>';
							foreach($visitData2 as $key=>$item){
								$html4.='<tr>
									<td>'.($key+1).'</td>
									<td>'.$item['client_name'].'</td>
									<td>'.$item['visit_count'].'</td>
								</tr>';
							}
							$html4.='</tbody>
						</table>
					</div>
				</div>';
		}
		else
		{
			$html4.=$noTaskHtml;
		}
	}

	$msg="Employee Record Get Successfully.";
	$msgcode=0;

	## Response
	$response = array("RESULT"=>$msgcode,"msg"=>$msg,"chartData3"=>$chartData3??[],"html4"=>$html4,"selected_employee"=>$selected_employee??'',"selected_employee_name"=>$selected_employee_name);
		
	echo json_encode($response);
	exit;
}
function generateRandomColor() {
	return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
if($get_actionType=="employee_visit_list")
{
	$id=$app->getPostVar("id");
	$purpose=$app->getPostVar("purpose");

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

	$searchQuery='';
	if($purpose=='Total Logistics Visits')
	{
		$searchQuery.='';
	}
	else if($purpose=='Total Collect Payment')
	{
		$searchQuery.=" AND employee_sample_pickup.collect_payment='Yes'";
	}
	else if($purpose=='Total Collect Sample')
	{
		$searchQuery.=" AND employee_sample_pickup.collect_sample='Yes'";
	}
	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_sample_pickup.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}

	$searchArray=$app->getPostVar('search');
	$searchValue = $searchArray['value']; // Search value

	if($searchValue != '')
	{
		$searchQuery .= " and (
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
	
	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." LEFT JOIN client on client.id=employee_sample_pickup.client_id left join employee on employee_sample_pickup.employee_id=employee.id where ".$table_name.".id!='0' and employee_sample_pickup.employee_id IN (".$id.") ".$searchQuery."");
	$totalRecords = $result[0]['allcount'];

	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." LEFT JOIN client on client.id=employee_sample_pickup.client_id left join employee on employee_sample_pickup.employee_id=employee.id where  ".$table_name.".id!='0' and employee_sample_pickup.employee_id IN (".$id.") ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];

	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_table->join_table("client", "left", array("company_name","mobile"), array("client_id"=>"id"));
	$obj_brand->join_table("employee", "left", array(), array("employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".id!='0' and employee_sample_pickup.employee_id IN (".$id.") ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	
	$data = array();
	for($i=0;$i<count($result);$i++)
	{

		$checkINTime='';
		$checkOutTime='';
		$obj_table = $app->load_model("employee_sample_pickup_update");
		$checkIN = $obj_table->execute("SELECT", false, "", "pickup_status='Check In' and employee_sample_pickup_id='".$result[$i]["id"]."'");
		if(count($checkIN)>0){
			$banner_img=$app->utility->get_image_path($checkIN[0]['checkin_photo'],'taskUpdate',"large");
			$checkINTime='<b>Check IN : </b>'.date('d-m-Y h:i A',strtotime($checkIN[0]['pickup_date']));
		}
		$checkOut= $obj_table->execute("SELECT", false, "", "pickup_status='Check Out' and employee_sample_pickup_id='".$result[$i]["id"]."'");
		if(count($checkOut)>0){
			$checkOutTime='<b>Check Out : </b>'.date('d-m-Y h:i A',strtotime($checkOut[0]['pickup_date']));
		}

		$sr=$i+1+$row;

		$status=$app->utility->get_employee_sample_pickup_status(["status"=>$result[$i]["status"]]);
		$summary='Collect Sample : <b>'.$result[$i]['collect_sample'].'</b>';
		$summary.='<br/>Collect Payment : <b>'.$result[$i]['collect_payment'].'</b>';
		$data[] = array
		(
			"id"=>$result[$i]["id"].'<br>'.$status['badge'],
			"employee_name"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_mobile'].' '.$result[$i]['employee_email'].' '.$result[$i]['master_designation_name'].'<br/>'.$checkINTime.'<br/>'.$checkOutTime,
			"client_company_name"=>'<b>'.$result[$i]['client_company_name'].'</b><br/>'.$result[$i]['client_email'].' '.$result[$i]['client_mobile'],
			"pickup_date"=>$result[$i]['pickup_date'],
			"summary"=>$summary,	
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

echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>"","msg"=>$msg));
?>