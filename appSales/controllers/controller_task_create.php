<?
class _task_create extends controller {
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

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('action'));
		
		if($employeeID=='' || $action=='' ) {
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($taskId!='') {
			$obj_model_employee_task_master = $this->app->load_model("employee_task_master");
			$obj_model_employee_task_master->join_table("employee_task_master_detail", "left", array(), array("id"=>"employee_task_master_id"));
			$employee_task_master = $obj_model_employee_task_master->execute("SELECT",false,"","employee_task_master.id='".$taskId."'");
			if(count($employee_task_master)<=0) {
				$message=array("message"=>"No Task Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
		} else {
			$employee_task_master=[];
		}

		if($action=='employeeSubmit')
		{
			if($this->app->getPostVar('taskSelectedEmployee')=='') {
				$message=array("message"=>"Please Select Employee","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$employee_ids=[];
			$taskSelectedEmployee=explode(',',$this->app->getPostVar('taskSelectedEmployee'));
			foreach($taskSelectedEmployee as $item)
			{
				if($this->app->utility->decrypt($item)!=0){
				array_push($employee_ids,$this->app->utility->decrypt($item));
				}
			}

			//check if task is new or task is edit
			if($taskId!='') {
				
				//for manual task add condition
				if($employee_task_master[0]['client_id']>0) {
					if(in_array($employee_task_master[0]['employee_primary_id'],$employee_ids)) {
						array_push($employee_ids,$employee_task_master[0]['employee_primary_id']);
					}
				}
				
				//update
				$data_t=array();
				$data_t['employee_ids']=implode(',',$employee_ids);
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master");
				$obj_model_employee_task_master->map_fields($data_t);
				$obj_model_employee_task_master->execute("UPDATE",false,"","id='".$taskId."'");		
			} else {
				//insert
				$data_t=array();
				$data_t['employee_ids']=implode(',',$employee_ids);
				$data_t['assign_by_employee_id']=$employeeID;
				$data_t['status']='Draft';
				$data_t['employee_primary_id']=count($employee_ids)>1?0:$employee_ids[0];
				$data_t['created_at']=date("Y-m-d H:i:s");
				$data_t['updated_at']=date("Y-m-d H:i:s");;
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master");
				$obj_model_employee_task_master->map_fields($data_t);
				$taskId=$obj_model_employee_task_master->execute("INSERT",false,"","");

				$data_t=array();
				$data_t['employee_task_master_id']=$taskId;
				$data_t['task_type']='Assign';
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master_detail");
				$obj_model_employee_task_master->map_fields($data_t);
				$obj_model_employee_task_master->execute("INSERT",false,"","");
			}
	
			$result=["taskId"=>$this->app->utility->encrypt($taskId)];
			$message=array("message"=>"Employee Selected.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='clientSubmit')
		{
			$purposeType=$this->app->getPostVar('purposeType');
			if($this->app->getPostVar('taskSelectedClient')=='') {
				$message=array("message"=>"Please Select Client","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
			$client_ids=[];
			$taskSelectedClient=explode(',',$this->app->getPostVar('taskSelectedClient'));
			foreach($taskSelectedClient as $item) {
				if($this->app->utility->decrypt($item)!=0){
					array_push($client_ids,$this->app->utility->decrypt($item));
				}
			}

			//in manual task they can select one client at a time
			if(count($client_ids)>1) {
				$message=array("message"=>"Please Select Any One","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			if($taskId!='') {
				//update
				$data_t=array();
				$data_t['client_id']=$client_ids[0];
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$data_t['purpose']=$purposeType;
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master");
				$obj_model_employee_task_master->map_fields($data_t);
				$obj_model_employee_task_master->execute("UPDATE",false,"","id='".$taskId."'");

			} else {

				
				//insert manual task
				$data_t=array();
				$data_t['employee_ids']=$employeeID;
				$data_t['assign_by_employee_id']=$employeeID;
				$data_t['client_id']=$client_ids[0];
				$data_t['status']='Active';
				$data_t['employee_primary_id']=$employeeID;
				$data_t['task_datetime']=date("Y-m-d H:i:s");
				$data_t['created_at']=date("Y-m-d H:i:s");
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$data_t['purpose']=$purposeType;
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master");
				$obj_model_employee_task_master->map_fields($data_t);
				$taskId=$obj_model_employee_task_master->execute("INSERT",false,"","");

				$data_t=array();
				$data_t['employee_task_master_id']=$taskId;
				$data_t['task_type']='Manual';
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master_detail");
				$obj_model_employee_task_master->map_fields($data_t);
				$obj_model_employee_task_master->execute("INSERT",false,"","");
			}
	
			$result=["taskId"=>$this->app->utility->encrypt($taskId)];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($action=='viewTaskData')
		{
			if(empty($taskId)) {
				$message=array("message"=>"Task Not Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$employees=explode(',',$employee_task_master[0]['employee_ids']);
			if(count($employees)>1) {
				//get task employee details start
				$cond="find_in_set(employee.id,'".$employee_task_master[0]['employee_ids']."')";
				$obj_model_employeeList = $this->app->load_model("employee");
				$obj_model_employeeList->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$obj_model_employeeList->join_table("city", "left", array("name"), array("city_id"=>"id"));
				$task_employee = $obj_model_employeeList->execute("SELECT",false,"",$cond);
				foreach($task_employee as $item)
				{
					$id=$this->app->utility->encrypt($item['id']);
					$image=$this->app->utility->get_image_url($item[0]["image"],'employee','large');
					$employeeList[]=array("id"=>$id,"name"=>$item['name'],"image"=>$image,"designation"=>$item['master_designation_name'],"city"=>$item['city_name']);
				}
				//get task employee details end
			} else {
				$employeeList=[];
			}
			
			//$employee_task_master
			$result=[
				"taskCheckInDate"=>empty($employee_task_master[0]['task_datetime'])?'':date('d-m-Y', strtotime($employee_task_master[0]['task_datetime'])),
				"taskCheckInTime"=>empty($employee_task_master[0]['task_datetime'])?'':date('h:i A', strtotime($employee_task_master[0]['task_datetime'])),
				"taskCheckOutDate"=>empty($employee_task_master[0]['employee_task_master_detail_check_out'])?'':date('d-m-Y', strtotime($employee_task_master[0]['employee_task_master_detail_check_out'])),
				"taskCheckOutTime"=>empty($employee_task_master[0]['employee_task_master_detail_check_out'])?'':date('d-m-Y', strtotime($employee_task_master[0]['employee_task_master_detail_check_out'])),
				"taskRemark"=>$employee_task_master[0]['task_remark'],
				"taskPurpose"=>$employee_task_master[0]['purpose'],
				"taskMeetingClient"=>$employee_task_master[0]['employee_task_master_detail_meeting_client_meet'],
				"taskPrimaryEmployeeId"=>$this->app->utility->encrypt($employee_task_master[0]['employee_primary_id']),
				"taskEmployee"=>$employeeList
			];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($action=='taskDataSubmit')
		{
			if(empty($taskId)) {
				$message=array("message"=>"Task Not Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$taskCheckInDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskCheckInDate'));
			$taskCheckInTime=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskCheckInTime'));
			$taskCheckOutTime=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskCheckOutTime'));
			$taskRemark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskRemark'));
			$taskMeetingClient=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskMeetingClient'));
			$taskPrimaryEmployeeId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskPrimaryEmployeeId'));
			$purposeType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('purposeType'));

			// if(empty($taskRemark) || empty($purposeType))
			// {
			// 	$message=array("message"=>"Please fill required data.","msgCode"=>"0");
			// 	$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			// 	echo $this->app->utility->indent($opt); exit;
			// }

			//insert manual task
			$data_t=array();
			if($employee_task_master[0]['status']=='Draft') {
				$data_t['status']='Active';
			}
			if($taskPrimaryEmployeeId!='') {
				$data_t['employee_primary_id']=$this->app->utility->decrypt($taskPrimaryEmployeeId);
			}
			$data_t['task_datetime']=date('Y-m-d', strtotime($taskCheckInDate)).' '.date('H:i:s', strtotime($taskCheckInTime));
			if($taskCheckOutTime!='') {
				$data_t['task_remark']='Manual Task';
			}else {
				$data_t['task_remark']=$taskRemark;
			}
			$data_t['purpose']=$purposeType;
			$data_t['updated_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("employee_task_master");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("UPDATE",false,"","id='".$taskId."'");
			
			$data_t=array();
			$data_t['check_in']=date('Y-m-d', strtotime($taskCheckInDate)).' '.date('H:i:s', strtotime($taskCheckInTime));
			if($taskCheckOutTime!='') {
			$data_t['check_out']=date('Y-m-d', strtotime($taskCheckInDate)).' '.date('H:i:s', strtotime($taskCheckOutTime));
			$data_t['meeting']=date('Y-m-d', strtotime($taskCheckInDate)).' '.date('H:i:s', strtotime($taskCheckOutTime));
			$data_t['meeting_client_meet']=$taskMeetingClient;
			$data_t['meeting_client_meet']=$taskMeetingClient;
			}
			$obj_model_employee_task_master=$this->app->load_model("employee_task_master_detail");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("UPDATE",false,"","employee_task_master_id='".$taskId."'");

			if($taskCheckOutTime!='') {
				//for complete direct checkin, meeting, checkout
				//$this->manualTask($taskId,$employeeID,$taskRemark);
			}

			if($taskCheckOutTime=='') 
			{
				//send push notification
				$obj_model_employee_task_master=$this->app->load_model("employee_task_master");
				$task=$obj_model_employee_task_master->execute("SELECT",false,"","id='".$taskId."'");
				$data['employee_ids']=$task[0]['employee_ids'];
				$title='New Task #'.$taskId.' assigned';
				$message=$task[0]['task_remark'];
				$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'NotificationListActivity');
				$this->app->utility->send_push($data);

				//notification data insert
				$data_t=array();
				$data_t['noti_type']='Task';
				$data_t['title']=$title;
				$data_t['description']=$message;
				$data_t['employee_ids']=$data['employee_ids'];
				$data_t['table_id']=$taskId;
				$data_t['created_by']=$employeeID;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$obj_model_employee_task_master=$this->app->load_model("notifications");
				$obj_model_employee_task_master->map_fields($data_t);
				$obj_model_employee_task_master->execute("INSERT");
			}

			$message=array("message"=>"success","msgCode"=>"1");
		}
		else
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}

	function manualTask($taskId,$employeeID,$taskRemark)
	{
		$obj_model_employee_task_master = $this->app->load_model("employee_task_master");
		$obj_model_employee_task_master->join_table("employee_task_master_detail", "left", array(), array("id"=>"employee_task_master_id"));
		$employee_task_master = $obj_model_employee_task_master->execute("SELECT",false,"","employee_task_master.id='".$taskId."'");
		
		$data_t=array();		
		$data_t['employee_task_master_id']=$taskId;
		$data_t['employee_id']=$employeeID;
		$data_t['activity']='Check In';
		$data_t['activity_time']=$employee_task_master[0]['employee_task_master_detail_check_in'];
		$obj_model_employee=$this->app->load_model("employee_task_master_update");
		$obj_model_employee->map_fields($data_t);
		$obj_model_employee->execute("INSERT");

		$data_t=array();
		$data_t['employee_task_master_id']=$taskId;
		$data_t['employee_id']=$employeeID;
		$data_t['activity']='Meeting';
		$data_t['meeting_status']='Complete';
		$data_t['meeting_remark']=$taskRemark;
		$data_t['meeting_client_meet']=$employee_task_master[0]['employee_task_master_detail_meeting_client_meet'];
		$data_t['activity_time']=$employee_task_master[0]['employee_task_master_detail_meeting'];
		$obj_model_employee=$this->app->load_model("employee_task_master_update");
		$obj_model_employee->map_fields($data_t);
		$obj_model_employee->execute("INSERT");

		$data_t=array();
		$data_t['employee_task_master_id']=$taskId;
		$data_t['employee_id']=$employeeID;
		$data_t['activity']='Check Out';
		$data_t['activity_time']=$employee_task_master[0]['employee_task_master_detail_check_out'];
		$obj_model_employee=$this->app->load_model("employee_task_master_update");
		$obj_model_employee->map_fields($data_t);
		$obj_model_employee->execute("INSERT");

		//update task status
		$data_t=array();	
		$data_t['status']='Completed';
		$obj_model_employee=$this->app->load_model("employee_task_master");
		$obj_model_employee->map_fields($data_t);
		$obj_model_employee->execute("UPDATE",false,"","id='".$taskId."'");
		
		return '';
	}
}
?>