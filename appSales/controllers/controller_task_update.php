<?
class _task_update extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$taskId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("taskId"));
		$taskId=$this->app->utility->decrypt($taskId);

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action")); //checkIn, checkOut, meeting

		if($taskId=='' || $employeeID=='' || $action=='')
		{
			$message=array("message"=>"Data is missing.","msgcode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$obj_model_employee_task = $this->app->load_model("employee_task_master");
		$obj_model_employee_task->join_table("employee_task_master_detail", "left", array(), array("id"=>"employee_task_master_id"));
		$task = $obj_model_employee_task->execute("SELECT",false,"","employee_task_master.status!='Trash' and employee_task_master.id='".$taskId."'","employee_task_master.id desc");
		if(count($task)<=0) { 
			$message=array("message"=>"No Task Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		
		if($action=='checkIn')
		{
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$checkUpdate=$obj_model_employee->execute("SELECT",false,"","employee_task_master_id='".$taskId."'","id desc");
			if(count($checkUpdate)>0 && $checkUpdate[0]['activity']!='End Journey'){
				$message=array("message"=>"Try again after closing app.","msgCode"=>"1");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$latitude=$this->app->getPostVar('latitude');
			$longitude=$this->app->getPostVar('longitude');
			$address='';
			if(!empty($latitude) && !empty($longitude)){
				//Send request and receive json data by address
				$geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyDfpQaNzc-kOJJtSW30BanO-QDQjNI7wq0'); 
				$output = json_decode($geocodeFromLatLong);
				$status = $output->status;
				//Get address from json data
				$address = ($status=="OK")?$output->results[1]->formatted_address:'';
				if(!empty($address)){
					$addressArr=explode(',',$address);
					array_shift($addressArr);
					$address=implode(",",$addressArr);
				}
			}

			$data_t=array();
			if($_FILES['checkInPhoto']['name']!='')
			{
				$checkin_photo=$this->app->utility->resize_single_image($_FILES['checkInPhoto']['name'],$_FILES['checkInPhoto']['tmp_name'],$this->app->get_user_config("taskUpdate"),'500');
				$data_t['checkin_photo']= $checkin_photo;
				$this->app->utility->remove_uploaded_file();
			}			
			$data_t['employee_task_master_id']=$taskId;
			$data_t['employee_id']=$employeeID;
			$data_t['activity']='Check In';
			$data_t['google_address']=$address;
			$data_t['activity_time']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$obj_model_employee->map_fields($data_t);
			$insID=$obj_model_employee->execute("INSERT");

			if($insID!=''){
				$obj_model_employee=$this->app->load_model("employee_task_master_update");
				$obj_model_employee->execute("UPDATE",false,"UPDATE employee_task_master_update SET google_address = '".$address."' WHERE id = '".$insID."'");
			}

			//update task status
			$data_t=array();	
			$data_t['status']='Inprogress';
			$obj_model_employee=$this->app->load_model("employee_task_master");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$taskId."'");
			
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else if($action=='startJourney')
		{
			$data_t=array();		
			$data_t['employee_task_master_id']=$taskId;
			$data_t['employee_id']=$employeeID;
			$data_t['activity']='Start Journey';
			$data_t['activity_time']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			//update task status
			$data_t=array();	
			$data_t['status']='Inprogress';
			$obj_model_employee=$this->app->load_model("employee_task_master");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$taskId."'");
			
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else if($action=='endJourney')
		{

			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$checkUpdate=$obj_model_employee->execute("SELECT",false,"","employee_task_master_id='".$taskId."'","id desc");
			if(count($checkUpdate)>0 && $checkUpdate[0]['activity']!='Start Journey'){
				$message=array("message"=>"Try again after closing app.","msgCode"=>"1");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();	
			$data_t['employee_task_master_id']=$taskId;
			$data_t['employee_id']=$employeeID;
			$data_t['activity']='End Journey';
			$data_t['activity_time']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else if($action=='meeting')
		{
			$meeting_remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("meetingRemark"));
			$meeting_status=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("meetingStatus"));
			$meeting_client_meet=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("meetingClientMeet"));

			$data_t=array();
			$data_t['employee_task_master_id']=$taskId;
			$data_t['employee_id']=$employeeID;
			$data_t['activity']='Meeting';
			$data_t['meeting_remark']=$meeting_remark;
			$data_t['meeting_status']=$meeting_status;
			$data_t['meeting_client_meet']=$meeting_client_meet;
			$data_t['activity_time']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			$message=array("message"=>"Updated Successfully.".$meeting_remark,"msgCode"=>"1");
		}
		else if($action=='checkOut')
		{
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$checkUpdate=$obj_model_employee->execute("SELECT",false,"","employee_task_master_id='".$taskId."'","id desc");
			//print_r();exit;
			if(count($checkUpdate)>0 && ($checkUpdate[0]['activity']=='Check In' || $checkUpdate[1]['activity']=='Check In')){}else{
				$message=array("message"=>"Try again after closing app.","msgCode"=>"1");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			if(count($checkUpdate)>0 && $checkUpdate[0]['activity']=='Check Out'){
				$message=array("message"=>"Try again after closing app.","msgCode"=>"1");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$meeting_remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("meetingRemark"));
			$meeting_status=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("meetingStatus"));
			$meeting_client_meet=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("meetingClientMeet"));

			$data_t=array();
			$data_t['employee_task_master_id']=$taskId;
			$data_t['employee_id']=$employeeID;
			$data_t['activity']='Check Out';
			$data_t['meeting_remark']=$meeting_remark;
			$data_t['meeting_status']=$meeting_status;
			$data_t['meeting_client_meet']=$meeting_client_meet;
			$data_t['activity_time']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_task_master_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			//update task status
			$data_t=array();	
			$data_t['status']='Completed';
			$obj_model_employee=$this->app->load_model("employee_task_master");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$taskId."'");

			//get start journey lat long
			$checkin_latitude=$checkUpdate[2]['latitude'];
			$checkin_longitude=$checkUpdate[2]['longitude'];
			$checkout_latitude=$this->app->getPostVar('latitude');
			$checkout_longitude=$this->app->getPostVar('longitude');
			$distance='';

			if(!empty($checkout_latitude) && !empty($checkout_longitude) && !empty($checkin_latitude) && !empty($checkin_longitude))
			{
 				$apiKey = GOOGLE_MAP_API_KEY;
				$directionsUrl = "https://maps.googleapis.com/maps/api/directions/json?origin=$checkin_latitude,$checkin_longitude&destination=$checkout_latitude,$checkout_longitude&key=$apiKey";
        
				$response = file_get_contents($directionsUrl);
				$data = json_decode($response, true);
		
				// Extract distance in KM
				if (!empty($data['routes'][0]['legs'][0]['distance']['text'])) {
					$distance = $data['routes'][0]['legs'][0]['distance']['text'];
				}

				//update task detail
				$data_t=array();	
				$data_t['distance_km']=$distance;
				$obj_model_employee=$this->app->load_model("employee_task_master_detail");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("UPDATE",false,"","employee_task_master_id='".$taskId."'");
			}
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>