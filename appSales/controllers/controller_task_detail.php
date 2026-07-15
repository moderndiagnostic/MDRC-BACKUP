<?
class _task_detail extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$taskId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("taskId"));
		$taskId=$this->app->utility->decrypt($taskId);

		if($employeeID!='' && $deviceType!='' && $taskId!='')
		{
			$obj_model_employee_task = $this->app->load_model("employee_task_master");
			$obj_model_employee_task->join_table("employee_task_master_detail", "left", array(), array("id"=>"employee_task_master_id"));
			$obj_model_employee_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$task = $obj_model_employee_task->execute("SELECT",false,"","employee_task_master.status!='Trash' and employee_task_master.id='".$taskId."'","employee_task_master.id desc");
			$item=$task[0];

			$taskOwner=$employeeID==$item['assign_by_employee_id']?true:false;
			$taskCheckInOwner=$employeeID==$item['employee_primary_id']?true:false;

			if(count($task)<=0) { 
				$message=array("message"=>"No Task Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			//get task employee details start
			$employees=explode(',',$item['employee_ids']);
			$cond=count($employees)>1?"find_in_set(employee.id,'".$item['employee_ids']."')":"employee.id='".$item['employee_primary_id']."'";
			$obj_model_employeeList = $this->app->load_model("employee");
			$obj_model_employeeList->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
			$task_employee = $obj_model_employeeList->execute("SELECT",false,"",$cond);
			foreach($task_employee as $emp)
			{
				$image=$this->app->utility->get_image_url($emp[0]["image"],'employee','large');
				$primary=$emp['id']==$item['employee_primary_id']?'Yes':'';
				$taskEmployee[]=[
					"name"=>$emp['name'],
					"image"=>$image,
					"designation"=>$emp['master_designation_name'],
					"primary"=>$primary
				];
			}
			//get task employee details end

			//get client details start
			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.id='".$item['client_id']."'","client.id desc limit 0,1");

			$clientImage=$this->app->utility->get_image_url($client[0]["client_image"],'client','large');
			$clientCompanyName=$client[0]['company_name'];
			$clientAddress=$client[0]['client_status']=='Client'?$client[0]['client_detail_area'].' '.$client[0]['city_name']:$client[0]['client_address_google_city'];
			//$clientAddress=$client[0]['client_detail_area'].' '.$client[0]['city_name'];
			$clientPhone=$client[0]['client_mobile']!=''?$client[0]['client_mobile']:'';
			$clientLatitude=$client[0]['client_address_google_latitude'];
			$clientLongitude=$client[0]['client_address_google_longitude'];
			$clientStatus=$client[0]['client_status'];
			//get client details end

			$taskDetail[]=[
				"number"=>"#".$item['id'],
				"status"=>$item['status'],
				"textStatusColor"=>"#d1e7dd",
				"textStatusBgColor"=>"#0f5031",
				"assignDate"=>date('d-m-Y h:i A', strtotime($item['task_datetime'])),
				"assignBy"=>$item['employee_name'],
				"clientImage"=>$clientImage,
				"clientCompanyName"=>$clientCompanyName,
				"clientPhone"=>$clientPhone,
				"clientAddress"=>$clientAddress,
				"clientLatitude"=>$clientLatitude,
				"clientLongitude"=>$clientLongitude,
				"clientTagName"=>$clientStatus,
				"clientTagColor"=>'#5ccdde',
				"taskDate"=>date('d-m-Y', strtotime($item['task_datetime'])),
				"taskTime"=>date('h:i A', strtotime($item['task_datetime'])),
				"taskDetail"=>$item['task_remark'],
				"purpose"=>$item['purpose'],
				"taskEmployee"=>$taskEmployee,
			];

			$getTaskUpdateDetails=$this->getTaskUpdate($item['id'],$item['employee_task_master_detail_task_type'],$taskOwner,$taskCheckInOwner);

			$result=["taskDetail"=>$taskDetail,"taskUpdate"=>$getTaskUpdateDetails['taskUpdate'],"taskButton"=>$getTaskUpdateDetails['taskButton'],"taskEditButton"=>$getTaskUpdateDetails['taskEditButton']];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}

	function getTaskUpdate($taskId,$taskType,$taskOwner,$taskCheckInOwner)
	{
		$taskSummaryViewFlag='No';
		$taskCheckInFlag=false;
		$taskCheckOutFlag=false;
		$taskMeetingFlag=false;
		$taskStartJourneyFlag=false;
		$taskEndJourneyFlag=false;

		$obj_model_task_master_update = $this->app->load_model("employee_task_master_update");
		$task_master_update = $obj_model_task_master_update->execute("SELECT",false,"","employee_task_master_update.employee_task_master_id='".$taskId."'","employee_task_master_update.id desc");
		
		foreach($task_master_update as $update)
		{
			if($update['activity']=='Check In') {
				$taskCheckInFlag=true;
				$taskSummaryViewFlag='Yes';
				$photo='';
				if($update['checkin_photo']!='' && file_exists(ABS_PATH.'/uploads/taskUpdate/'.$update['checkin_photo']))
				{
					$photo=SERVER_ROOT.'/uploads/taskUpdate/'.$update['checkin_photo'];
				}
				$taskCheckIn[]=[
					"date"=>date('d-m-Y', strtotime($update['activity_time'])),
					"dime"=>date('h:i A', strtotime($update['activity_time'])),
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>'0 KM',
					"photo"=>$photo
				];
			}

			if($update['activity']=='Check Out') {
				$taskCheckOutFlag=true;
				$taskSummaryViewFlag='Yes';
				$taskCheckOut[]=[
					"date"=>date('d-m-Y', strtotime($update['activity_time'])),
					"dime"=>date('h:i A', strtotime($update['activity_time'])),
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>'0 KM',
				];
			}

			if($update['activity']=='Check Out') {
				$taskMeetingFlag=true;
				$taskSummaryViewFlag='Yes';
				$taskMeeting[]=[
					"taskMeeting"=>$update['meeting_remark'],
					"date"=>date('d-m-Y', strtotime($update['activity_time'])),
					"dime"=>date('h:i A', strtotime($update['activity_time'])),
					"meetDoctor"=>$update['meeting_client_meet'],
					"meetingStatus"=>$update['meeting_status'],
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>'0 KM',
				];
			}

			if($update['activity']=='Start Journey') {
				$taskStartJourneyFlag=true;
				$taskSummaryViewFlag='Yes';
				$taskStartJourney[]=[
					"date"=>date('d-m-Y', strtotime($update['activity_time'])),
					"dime"=>date('h:i A', strtotime($update['activity_time'])),
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>'0 KM',
				];
			}
 
			if($update['activity']=='End Journey') {
				$taskEndJourneyFlag=true;
				$taskSummaryViewFlag='Yes';
				$taskEndJourney[]=[
					"date"=>date('d-m-Y', strtotime($update['activity_time'])),
					"dime"=>date('h:i A', strtotime($update['activity_time'])),
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>'0 KM',
				];
			}
		}

		if(!$taskCheckInFlag) {
			$taskCheckIn[]=[
				"date"=>'',
				"dime"=>'',
			];
		}
		
		if(!$taskCheckOutFlag) {
			$taskCheckOut[]=[
				"date"=>'',
				"dime"=>'',
			];
		}

		if(!$taskMeetingFlag) {
			$taskMeeting[]=[
				"taskMeeting"=>'',
				"date"=>'',
				"dime"=>'',
				"meetDoctor"=>'',
				"meetingStatus"=>'',
			];
		}

		$taskUpdate[]=[
			"taskSummaryView"=>$taskSummaryViewFlag,
			"taskCheckIn"=>!empty($taskCheckIn)?$taskCheckIn:[],
			"taskCheckOut"=>!empty($taskCheckOut)?$taskCheckOut:[],
			"taskMeeting"=>!empty($taskMeeting)?$taskMeeting:[],
			"taskStartJourney"=>!empty($taskStartJourney)?$taskStartJourney:[],
			"taskEndJourney"=>!empty($taskEndJourney)?$taskEndJourney:[],
		];

		//only assign to employee can cehck in
		if(!$taskCheckInOwner) { 
			$taskCheckInFlag=true;
			$taskCheckOutFlag=true;
			$taskMeetingFlag=true;
			$taskStartJourneyFlag=true;
			$taskEndJourneyFlag=true;
		}
		
		$taskButton[]=[
			"taskButtonOption"=>($taskCheckInFlag && $taskCheckOutFlag) ? 'No':'Yes',
			"taskStartJourney"=>(!$taskCheckInFlag && !$taskCheckOutFlag  && !$taskStartJourneyFlag && !$taskEndJourneyFlag) ? 'Yes':'No',
			"taskEndJourney"=>(!$taskCheckOutFlag && !$taskCheckInFlag && $taskStartJourneyFlag && !$taskEndJourneyFlag) ? 'Yes':'No',
			"taskCheckIn"=>(!$taskCheckInFlag && !$taskCheckOutFlag  && $taskEndJourneyFlag && $taskStartJourneyFlag) ? 'Yes':'No',
			"taskCheckOut"=>(!$taskCheckOutFlag && $taskCheckInFlag && $taskStartJourneyFlag && $taskEndJourneyFlag) ? 'Yes':'No',
			"taskMeeting"=>($taskCheckInFlag && !$taskCheckOutFlag) ? 'No':'No',
		];

		$taskSummaryViewFlag=($taskOwner)?$taskSummaryViewFlag:'Yes'; //only task owner can edit task
		$taskEditButton[]=[
			"taskEditButtonOption"=>$taskSummaryViewFlag=='No' ? 'Yes':'No',
			"taskEdit"=>$taskSummaryViewFlag=='No' ? 'Yes':'No',
			"taskDelete"=>$taskSummaryViewFlag=='No' ? 'Yes':'No',
			"taskType"=>$taskType=='Assign'?'Auto':'', // as per task edit option come
		];

		return ["taskEditButton"=>$taskEditButton,"taskButton"=>$taskButton,"taskUpdate"=>$taskUpdate];
	}
}
?>