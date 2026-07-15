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
$noTaskHtml='<div class="col-sm-12 col-md-12 col-lg-12 mb-3">
				<div class="card p-3 rounded-3 border-0 hover-shadow">
					<div class="text-center">
					<h6 class="text-uppercase text-muted text-xs mb-2">
						No Task Performed.
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
	//$employeeId=1;

	$obj_table_tble= $app->load_model('employee');
	$employee= $obj_table_tble->execute("SELECT", false, "","id='".$employeeId."'");
	
	$subEmployee=$app->utility->getSubEmployee($employee[0]['lms_employee_id']);
	$subEmployeeId=array_column($subEmployee,'id');

	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_task_master.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}

	$taskDataSub=[];
	if(count($subEmployeeId)>0)
	{
		$obj_table_tble= $app->load_model('employee_task_master');
		$taskDataSub= $obj_table_tble->execute("SELECT", false, "SELECT purpose, COUNT(*) AS task_count FROM employee_task_master WHERE employee_primary_id IN (".implode(',',$subEmployeeId).") ".$searchQuery." GROUP BY purpose");
	}
	
	foreach($taskDataSub as $taskSub)
	{
		$chartData2[!empty($taskSub['purpose'])?$taskSub['purpose']:'Other']=!empty($taskSub['task_count'])?$taskSub['task_count']:0;
		$chartColor[]=generateRandomColor();
	}

	$chartData2=[
		'chartLabel'=>array_keys($chartData2),
		'chartValue'=>array_values($chartData2),
		'chartColor'=>$chartColor
	];

	$html2='';
	if(count($taskDataSub)>0)
	{
		foreach($taskDataSub as $taskSub)
		{
			$html2.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
						<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',$subEmployeeId).'" data-purpose="'.$taskSub['purpose'].'" data-name="Team">
							<div class="card p-3 rounded-3 border-0 hover-shadow">
								<div class="text-center">
								<h6 class="text-uppercase text-muted text-xs mb-2">
									'.($taskSub['purpose'] ? $taskSub['purpose'] : 'Other').'
								</h6>
								<div class="d-flex justify-content-center align-items-center">
									<h3 class="mb-0"> '. $taskSub['task_count'] .'</h3>
								</div>
								</div>
							</div>
						</a>
					</div>';
		}
	}
	else
	{
		$html2.=$noTaskHtml;
	}
	
	
	

	$obj_table_tble= $app->load_model('employee_task_master');
	$taskData= $obj_table_tble->execute("SELECT", false, "SELECT purpose, COUNT(*) AS task_count FROM employee_task_master WHERE employee_primary_id = '".$employeeId."' ".$searchQuery." GROUP BY purpose");
				
	foreach($taskData as $task)
	{
		$chartLabel[!empty($task['purpose'])?$task['purpose']:'Other']=!empty($task['task_count'])?$task['task_count']:0;
		$chartColor[]=generateRandomColor();
	}

	
	$chartData1=[
		'chartLabel'=>array_keys($chartLabel),
		'chartValue'=>array_values($chartLabel),
		'chartColor'=>$chartColor
	];

	$html1='';
	if(count($taskData)>0)
	{
		foreach($taskData as $task)
		{
			$html1.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
						<a href="javascript:void(0)" class="viewTask" data-id="'.$employeeId.'" data-purpose="'.$task['purpose'].'" data-name="'.$employee[0]['name'].'">
							<div class="card p-3 rounded-3 border-0 hover-shadow">
								<div class="text-center">
								<h6 class="text-uppercase text-muted text-xs mb-2">
									'.($task['purpose'] ? $task['purpose'] : 'Other').'
								</h6>
								<div class="d-flex justify-content-center align-items-center">
									<h3 class="mb-0"> '. $task['task_count'] .'</h3>
								</div>
								</div>
							</div>
						</a>
					</div>';
		}
	}
	else
	{
		$html1.=$noTaskHtml;
	}

	$html3='';
	if(count(array_merge($taskData,$taskDataSub))>0)
	{
		foreach(array_merge($taskData,$taskDataSub) as $allTaskData)
		{
			$html3.='<div class="col-sm-6 col-md-4 col-lg-3 mb-3">
						<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',array_merge($subEmployeeId,[$employeeId])).'" data-purpose="'.$allTaskData['purpose'].'" data-name="All Team">
							<div class="card p-3 rounded-3 border-0 hover-shadow">
								<div class="text-center">
								<h6 class="text-uppercase text-muted text-xs mb-2">
									'.($allTaskData['purpose'] ? $allTaskData['purpose'] : 'Other').'
								</h6>
								<div class="d-flex justify-content-center align-items-center">
									<h3 class="mb-0"> '. $allTaskData['task_count'] .'</h3>
								</div>
								</div>
							</div>
						</a>
					</div>';
		}
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
		$searchQuery.=" AND date(employee_task_master.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}
	$html4='';
	if(!empty($selected_employee))
	{
		$obj_table_tble= $app->load_model('employee');
		$employee= $obj_table_tble->execute("SELECT", false, "","id='".$selected_employee."'");

		$obj_table_tble= $app->load_model('employee_task_master');
		$taskData2= $obj_table_tble->execute("SELECT", false, "SELECT purpose, COUNT(*) AS task_count FROM employee_task_master WHERE employee_primary_id = '".$selected_employee."' ".$searchQuery." GROUP BY purpose");
					
		foreach($taskData2 as $task)
		{
			$chartLabel[!empty($task['purpose'])?$task['purpose']:'Other']=!empty($task['task_count'])?$task['task_count']:0;
			$chartColor[]=generateRandomColor();
		}

		$chartData3=[
			'chartLabel'=>array_keys($chartLabel),
			'chartValue'=>array_values($chartLabel),
			'chartColor'=>$chartColor
		];

		if(count($taskData2)>0)
		{
			foreach($taskData2 as $task)
			{
				$html4.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
							<a href="javascript:void(0)" class="viewTask" data-id="'.$selected_employee.'" data-purpose="'.$task['purpose'].'" data-name="'.$employee[0]['name'].'">
								<div class="card p-3 rounded-3 border-0 hover-shadow">
									<div class="text-center">
									<h6 class="text-uppercase text-muted text-xs mb-2">
										'.($task['purpose'] ? $task['purpose'] : 'Other').'
									</h6>
									<div class="d-flex justify-content-center align-items-center">
										<h3 class="mb-0"> '. $task['task_count'] .'</h3>
									</div>
									</div>
								</div>
							</a>
						</div>';
			}
		}
		else
		{
			$html4.=$noTaskHtml;
		}


		$subEmployee=$app->utility->getSubEmployee($employee[0]['lms_employee_id']);
		$subEmployeeId=array_column($subEmployee,'id');

		$taskDataSub=[];
		if(count($subEmployeeId)>0)
		{
			$obj_table_tble= $app->load_model('employee_task_master');
			$taskDataSub= $obj_table_tble->execute("SELECT", false, "SELECT purpose, COUNT(*) AS task_count FROM employee_task_master WHERE employee_primary_id IN (".implode(',',$subEmployeeId).") ".$searchQuery." GROUP BY purpose");
		}
		
		foreach($taskDataSub as $taskSub)
		{
			$chartData4[!empty($taskSub['purpose'])?$taskSub['purpose']:'Other']=!empty($taskSub['task_count'])?$taskSub['task_count']:0;
			$chartColor[]=generateRandomColor();
		}

		$chartData4=[
			'chartLabel'=>array_keys($chartData4),
			'chartValue'=>array_values($chartData4),
			'chartColor'=>$chartColor
		];

		$html5='';
		if(count($taskDataSub)>0)
		{
			foreach($taskDataSub as $taskSub)
			{
				$html5.='<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
							<a href="javascript:void(0)" class="viewTask" data-id="'.implode(',',$subEmployeeId).'" data-purpose="'.$taskSub['purpose'].'" data-name="Team">
								<div class="card p-3 rounded-3 border-0 hover-shadow">
									<div class="text-center">
									<h6 class="text-uppercase text-muted text-xs mb-2">
										'.($taskSub['purpose'] ? $taskSub['purpose'] : 'Other').'
									</h6>
									<div class="d-flex justify-content-center align-items-center">
										<h3 class="mb-0"> '. $taskSub['task_count'] .'</h3>
									</div>
									</div>
								</div>
							</a>
						</div>';
			}
		}
		else
		{
			$html5.=$noTaskHtml;
		}


	}

	$msg="Employee Record Get Successfully.";
	$msgcode=0;

	## Response
	$response = array("RESULT"=>$msgcode,"msg"=>$msg,"chartData3"=>$chartData3??[],"html4"=>$html4,"selected_employee"=>$selected_employee??'',"selected_employee_name"=>$selected_employee_name,"chartData4"=>$chartData4??[],"html5"=>$html5);
		
	echo json_encode($response);
	exit;
}
function generateRandomColor() {
	return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
if($get_actionType=="employee_task_list")
{
	$id=$app->getPostVar("id");
	$purpose=$app->getPostVar("purpose");

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

	$searchQuery='';
	if($start_date!='')
	{
		$searchQuery.=" AND date(employee_task_master.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
	}

	$searchArray=$app->getPostVar('search');
	$searchValue = $searchArray['value']; // Search value

	if($searchValue != '')
	{
		$searchQuery = " and (
		employee.name like '%".$searchValue."%' or 
		client.company_name like '%".$searchValue."%' or 	
		employee_task_master.purpose like '%".$searchValue."%' or
		employee_task_master.status like '%".$searchValue."%' )";
	}
	
	## Total number of records without filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." LEFT JOIN client on client.id=employee_task_master.client_id left join employee on employee_task_master.employee_primary_id=employee.id where ".$table_name.".id!='0' and employee_task_master.employee_primary_id IN (".$id.") and employee_task_master.purpose='".$purpose."'".$searchQuery."");
	$totalRecords = $result[0]['allcount'];

	## Total number of records with filtering
	$obj_table = $app->load_model($table_name);
	$result = $obj_table->execute("SELECT", false, "SELECT count(*) as allcount from ".$table_name." LEFT JOIN client on client.id=employee_task_master.client_id left join employee on employee_task_master.employee_primary_id=employee.id where  ".$table_name.".id!='0' and employee_task_master.employee_primary_id IN (".$id.") and employee_task_master.purpose='".$purpose."' ".$searchQuery);
	$totalRecordwithFilter = $result[0]['allcount'];

	## Fetch records
	$obj_brand = $app->load_model($table_name);
	$obj_table->join_table("client", "left", array("company_name","mobile"), array("client_id"=>"id"));
	$obj_brand->join_table("employee", "left", array(), array("employee_primary_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "".$table_name.".id!='0' and employee_task_master.employee_primary_id IN (".$id.") and employee_task_master.purpose='".$purpose."' ".$searchQuery.""," ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage." ");
	
	$data = array();
	for($i=0;$i<count($result);$i++)
	{
		
		$sr=$i+1+$row;

		$banner_img='';
		$checkINTime='';
		$checkOutTime='';
		$checkINLocation='';
		$obj_table = $app->load_model("employee_task_master_update");
		$checkIN = $obj_table->execute("SELECT", false, "", "employee_task_master_id='".$result[$i]["id"]."'");

		if(count($checkIN)>0)
		{
			foreach($checkIN as $task_update)
			{
				if($task_update['activity']=='Check In')
				{
					$banner_img=$app->utility->get_image_path($task_update['checkin_photo'],'taskUpdate',"large");
					$checkINTime='<b>Check IN : </b>'.date('d-m-Y h:i A',strtotime($task_update['activity_time']));
					$checkINLocation='<a href="https://www.google.com/maps/search/?api=1&query='.$task_update['latitude'].','.$task_update['longitude'].'" target="_blank">&#128205; Check In</a>';
				}
				if($task_update['activity']=='Check Out')
				{
					$checkOutTime='<b>Check Out : </b>'.date('d-m-Y h:i A',strtotime($task_update['activity_time']));
				}
			}
		}

		$created_at= date('d-m-Y h:i A', strtotime($result[$i]['created_at']));
		$status=$app->utility->getTaskStatusHtml($result[$i]["status"]);
		$detail_btn='<a href="index.php?view=task_detail&id='.$result[$i]['id'].'" class="btn btn-xs btn-primary btn-icon mg-r-5">Detail</a>';	
		$option='<div class="btn-toolbar"><div>'.$detail_btn.' '.$edit_btn.''.$delete_btn.'</div></div>';
		$data[] = array
		(
			"id"=>$result[$i]["id"].'<br>'.$status,
			"client"=>'<b>'.$result[$i]['client_company_name'].'</b><br/>'.$result[$i]['client_mobile'],
			"employee"=>'<b>'.$result[$i]['employee_name'].'</b><br/>'.$result[$i]['employee_lms_employee_code'].'<br/>'.$checkINTime.'<br/>'.$checkOutTime,
			"purpose"=>$result[$i]['purpose'],
			"created_at"=>$created_at,
			"image"=>'<a href="'.$banner_img.'" class="image-popup"><img src="'.$banner_img.'" class="wd-100" /></a>'.$checkINLocation
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