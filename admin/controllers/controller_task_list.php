<?php
class _task_list extends controller
{
	function init()
	{
		if($this->app->getCurrentAction()=="")
		{
			$this->load_data();
		}
	}

	function onload()
	{
	}	
		
	function load_data()
	{

	}	

	function downloadTask()
	{
		
		$start_date=$this->app->getGetVar('start_date');
		$end_date=$this->app->getGetVar('end_date');

		$dateDiff=$this->app->utility->DateDiff(strtotime($end_date),strtotime($start_date));
		if($dateDiff>45){
			$this->app->redirect("index.php?view=task_list");
		}
		else
		{
			$searchQuery='';
			if($start_date!='')
			{
				$searchQuery=" AND date(employee_task_master.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
			}
			$this->app->no_html=true;
			$obj_excel=$this->app->load_module("PHPExcel");
			$ExeclHeads=array("ID","Client Name","Client Panel ID","Client Mobile","Client Email","Assign By Employee Name","Assign By Employee Code","Assign To Employee Name",
			"Assign To Employee Code","Purpose","Task Remark","Task Datetime","Status","Check IN","Check Out","Meeting","Meeting Status","Meeting To Client","Total Time","Total Distance","Google Address","Check In Latitude","Check In Longitude");
	
			$obj_table = $this->app->load_model("employee_task_master");
			$obj_table->join_table("employee_task_master_detail", "left", array(), array("id"=>"employee_task_master_id"));
			$obj_table->join_table("client", "left", array("company_name","mobile","panel_id","email"), array("client_id"=>"id"));
			$obj_table->join_table("employee", "left", array("name","lms_employee_code"), array("assign_by_employee_id"=>"id"));
			$obj_table->join_table(["employee"=>"EMPLOYEE"], "left", array("name","lms_employee_code"), array("employee_primary_id"=>"id"));
			$result = $obj_table->execute("SELECT", false, "", "employee_task_master.status!='Draft' and employee_task_master.status!='Trash' ".$searchQuery."","employee_task_master.id desc");
			$diff='';
			$ucount=1;
			
			foreach($result as $user)
			{
				$task_date=date("d-m-Y H:i:s", strtotime($user['task_datetime']));
				$check_in=date("d-m-Y H:i:s", strtotime($user['employee_task_master_detail_check_in']));
				$check_out=date("d-m-Y H:i:s", strtotime($user['employee_task_master_detail_check_out']));
				$meeting=date("d-m-Y H:i:s", strtotime($user['employee_task_master_detail_meeting']));
				
				
				/* $obj_table = $this->app->load_model("employee_task_master_update");
				$task_update = $obj_table->execute("SELECT", false, "", "(activity='Start Journey' or activity='Check Out') and employee_task_master_id='".$user['id']."'","id ASC");
				
				if(count($task_update)==2)
				{
					
					$start_journey_latitude=$task_update[0]['latitude'];
					$start_journey_longitude=$task_update[0]['longitude'];
					$check_out_latitude=$task_update[1]['latitude'];
					$check_out_longitude=$task_update[1]['longitude'];
				
					$distance=$this->app->utility->getDistance($start_journey_latitude,$start_journey_longitude,$check_out_latitude,$check_out_longitude,'K');
					$diff=$this->app->utility->getTimeDiff(["startTime"=>$task_update[0]['activity_time'],"endTime"=>$task_update[1]['activity_time']]);
	
				}else{
					$distance="";
				}
				
				$googleAddress='';
				$obj_table = $this->app->load_model("employee_task_master_update");
				$googleAddressCheck = $obj_table->execute("SELECT", false, "", "activity='Check In' and employee_task_master_id='".$user['id']."'","id ASC");
			
				if(count($googleAddressCheck)>0) {
					if($googleAddressCheck[0]['google_address']==''){
						if($googleAddressCheck[0]['latitude']!='' && $googleAddressCheck[0]['id']>13206){
							$googleAddress=$this->updateCheckInGoogleAddress($googleAddressCheck[0]);
						}
					}else{
						$googleAddress=$googleAddressCheck[0]['google_address'];
					}
				}
				
				$check_in=count($googleAddressCheck)>0?date("d-m-Y h:i a", strtotime($googleAddressCheck[0]['activity_time'])):"";
				$check_out=count($task_update)==2?date("d-m-Y h:i a", strtotime($task_update[1]['activity_time'])):""; */
	//=================================================================================================================================================================================
				$lat='';
				$long='';
				$obj_table = $this->app->load_model("employee_task_master_update");
				$task_update = $obj_table->execute("SELECT", false, "", "employee_task_master_id='".$user['id']."'","id ASC");
	
				foreach($task_update as $taskUpdate){
					if($taskUpdate['activity']=='Start Journey') {
					$start_journey_latitude=$taskUpdate['latitude'];
					$start_journey_longitude=$taskUpdate['longitude'];
					$check_in=$taskUpdate['activity_time']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['activity_time'])):"";
					}
					if($taskUpdate['activity']=='Check In') {
						$googleAddress=$taskUpdate['google_address'];
						$check_in=$taskUpdate['activity_time']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['activity_time'])):"";
						$check_out=$taskUpdate['activity_time']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['activity_time'])):"";
						$lat=$taskUpdate['latitude'];
						$long=$taskUpdate['longitude'];
					}
					if($taskUpdate['activity']=='Check Out') {
						$check_out_latitude=$taskUpdate['latitude'];
						$check_out_longitude=$taskUpdate['longitude'];
						$check_out=$taskUpdate['activity_time']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['activity_time'])):"";
					}
				}
	
				$distance="";
				if($check_out_latitude!='')
				{
					$distance=$this->app->utility->getDistance($start_journey_latitude,$start_journey_longitude,$check_out_latitude,$check_out_longitude,'K');
					$diff=$this->app->utility->getTimeDiff(["startTime"=>$task_update[0]['activity_time'],"endTime"=>$task_update[1]['activity_time']]);
				}
	
				$data_array[]=array(
					"ID"=>$user['id'],
					"Client Name"=>$user['client_company_name'],
					"Client Panel ID"=>$user['client_panel_id'],
					"Client Mobile"=>$user['client_mobile'],
					"Client Email"=>$user['client_email'],
					"Assign By Employee Name"=>$user['employee_name'],
					"Assign By Employee Code"=>$user['employee_lms_employee_code'],
					"Assign To Employee Name"=>$user['EMPLOYEE_name'],
					"Assign To Employee Code"=>$user['EMPLOYEE_lms_employee_code'],
					"Purpose"=>$user['purpose'],
					"Task Remark"=>$user['task_remark'],
					"Task Datetime"=>$task_date,"Status"=>$user['status'],
					"Check IN"=>$check_in,
					"Check Out"=>$check_out,
					"Meeting"=>$meeting,
					"Meeting Status"=>$user['employee_task_master_detail_meeting_status'],
					"Meeting To Client"=>$user['employee_task_master_detail_meeting_client_meet'],
					"Total Time"=>$diff,
					"Total Distance"=>($user['id']>111298)?$user['employee_task_master_detail_distance_km']:$distance,
					"Google Address"=>$googleAddress,
					"Check In Latitude"=>$lat,
					"Check In Longitude"=>$long,
				);
				
				$ucount++;
			}
			
			$filename="FieldTaskList-".date('d-m-Y');
			$this->app->utility->export_excel($ExeclHeads,$data_array,$ExeclHeads,$filename,$ExeclHeads);
		}
		
	}
	
	/* function updateCheckInGoogleAddress($data)
	{
		return "";   
		exit;
		
		$latitude=$data['latitude'];
		$longitude=$data['longitude'];
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
			
			//Return address of the given latitude and longitude
			if(!empty($address)){
				
				$data_t=array();
				$data_t['google_address']=$address;
				$obj_model=$this->app->load_model("employee_task_master_update");
				$obj_model->map_fields($data_t);
				$obj_model->execute("UPDATE",false,"","activity='Check In' and employee_task_master_id='".$data['employee_task_master_id']."' and id='".$data['id']."'");

				return $address;
			}else{
				return "";
			}
		}else{
			return "";   
		}
	} */
}	
?>