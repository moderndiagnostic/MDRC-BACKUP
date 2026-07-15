<?
class _client_logistic_update extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$clientId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("clientId"));
		$clientId=$this->app->utility->decrypt($clientId);

		$employeeAssignID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("employeeAssignID"));
		$employeeAssignID=$this->app->utility->decrypt($employeeAssignID);

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('action'));

		$samplePickupDays=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('sample_pickup'));
		$samplePickupFrequency=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('sample_pickup_frequency'));
		
		if($employeeID=='' || $action=='' && $employeePhone=='') {
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($action=='Delete')
		{
			$clientId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("clientId"));
			$clientId=$this->app->utility->decrypt($clientId);

			if($clientId=='') {
				$message=array("message"=>"Please Select Client.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model_client_check = $this->app->load_model("client_logistic_assign");
			$checkClient = $obj_model_client_check->execute("SELECT",false,"","client_id='".$clientId."'","id desc limit 0,1");
			if(count($checkClient)<=0) {
				$message=array("message"=>"Something Gone Wrong.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			//update
			$data_t=array();
			$data_t['employee_id']='0';
			$data_t['request_status']='Pending';
			$obj_model_client=$this->app->load_model("client_logistic_assign");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$checkClient[0]['id']."'");
			
			$data_t=array();
			$data_t['client_id']=$clientId;
			$data_t['client_logistic_assign_id']=$checkClient[0]['id'];
			$data_t['employee_id']=$employeeAssignID;
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['action']="Delete";
			$data_t['title']="Logistic Person Removed.";
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign_history");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("INSERT",false,"","");
			
			$message='Logistic Assigned Removed.';
			$message=array("message"=>$message,"msgCode"=>"1");
		}
		else if($action=='Assign')
		{
			if($clientId=='' || $employeeAssignID=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			$data_t['client_id']=$clientId;
			$data_t['logistic_manager_employee_id']=$employeeAssignID;
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$data_t['request_status']='Pending'; 
			$obj_model_client=$this->app->load_model("client_logistic_assign");
			$obj_model_client->map_fields($data_t);
			$ins=$obj_model_client->execute("INSERT",false,"","");
			
			$data_t=array();
			$data_t['client_logistic_assign_id']=$ins;
			$data_t['client_id']=$clientId;
			$data_t['logistic_manager_employee_id']=$employeeAssignID;
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['action']="Assign";
			$data_t['title']="Logistic Manager Assign";
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign_history");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("INSERT",false,"","");

			//update
			$data_t=array();
			$data_t['sample_pickup']=$samplePickupDays;
			$data_t['sample_pickup_frequency']=$samplePickupFrequency;
			$obj_model_client=$this->app->load_model("client_detail");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","client_id='".$clientId."'");
			
			//send push notification
			$obj_model_client=$this->app->load_model("client");
			$client=$obj_model_client->execute("SELECT",false,"","id='".$clientId."'");

			$data['employee_ids']=$employeeAssignID;
			$title='New Logistic Request for '.$client[0]['company_name'];
			$message='Please Review & Assign Logistic.';
			$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'NotificationListActivity');
			$this->app->utility->send_push($data);

			//notification data insert
			$data_t=array();
			$data_t['noti_type']='Logistic Assign';
			$data_t['title']=$title;
			$data_t['description']=$message;
			$data_t['employee_ids']=$data['employee_ids'];
			$data_t['table_id']=$clientId;
			$data_t['created_by']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("notifications");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("INSERT");

			$message=array("message"=>"Logistic Person Assigned.","msgCode"=>"1");
		}
		else if($action=='Edit')
		{
			if($clientId=='' || $employeeAssignID=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model_client_check = $this->app->load_model("client_logistic_assign");
			$checkClient = $obj_model_client_check->execute("SELECT",false,"","client_id='".$clientId."'","id desc limit 0,1");
			if(count($checkClient)<=0) {
				$message=array("message"=>"Something Gone Wrong.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			$data_t['client_id']=$clientId;
			$data_t['employee_id']=$employeeAssignID;
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$checkClient[0]['id']."'");
			
			$data_t=array();
			$data_t['client_logistic_assign_id']=$checkClient[0]['id'];
			$data_t['logistic_manager_employee_id']=$checkClient[0]['logistic_manager_employee_id'];
			$data_t['client_id']=$clientId;
			$data_t['employee_id']=$employeeAssignID;
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['action']="Assign";
			$data_t['title']="Logistic Person Re-assign";
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign_history");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("INSERT",false,"","");

			$message=array("message"=>"Logistic Person Assigned.","msgCode"=>"1");
		}
		else if($action=='Accept')
		{
			if($clientId=='' || $employeeID=='' || $employeeAssignID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model_client_check = $this->app->load_model("client_logistic_assign");
			$checkClient = $obj_model_client_check->execute("SELECT",false,"","client_id='".$clientId."'","id desc limit 0,1");
			if(count($checkClient)<=0) {
				$message=array("message"=>"Something Gone Wrong.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			$data_t['employee_id']=$employeeAssignID;
			$data_t['request_status']='Active';
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$checkClient[0]['id']."'");

			$data_t=array();
			$data_t['logistic_manager_employee_id']=$checkClient[0]['id'];
			$data_t['client_id']=$clientId;
			$data_t['logistic_manager_employee_id']=$checkClient[0]['logistic_manager_employee_id'];
			$data_t['employee_id']=$checkClient[0]['employee_id'];
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['action']="Accept";
			$data_t['title']="Logistic Manager Accepted.";
			$data_t['remark']="";
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign_history");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("INSERT",false,"","");
			
			$data_t=array();
			$data_t['logistic_manager_employee_id']=$checkClient[0]['id'];
			$data_t['client_id']=$clientId;
			$data_t['logistic_manager_employee_id']=$checkClient[0]['logistic_manager_employee_id'];
			$data_t['employee_id']=$checkClient[0]['employee_id'];
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['action']="Accept";
			$data_t['title']="Logistic Person Assigned.";
			$data_t['remark']="";
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign_history");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("INSERT",false,"","");

			//send push notification
			$obj_model_client=$this->app->load_model("client");
			$client=$obj_model_client->execute("SELECT",false,"","id='".$clientId."'");

			$data['employee_ids']=$checkClient[0]['assign_by_employee_id'];
			$title='Request for '.$client[0]['company_name'].' Approved';
			$message='Logistic Person assigned.';
			$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'NotificationListActivity');
			$this->app->utility->send_push($data);

			//notification data insert
			$data_t=array();
			$data_t['noti_type']='Client Detail';
			$data_t['title']=$title;
			$data_t['description']=$message;
			$data_t['employee_ids']=$data['employee_ids'];
			$data_t['table_id']=$clientId;
			$data_t['created_by']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("notifications");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("INSERT");

			$message=array("message"=>"Logistic Person Approved.","msgCode"=>"1");
		}
		else if($action=='Reject')
		{
			if($clientId=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
			$remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("remark"));

			$obj_model_client_check = $this->app->load_model("client_logistic_assign");
			$checkClient = $obj_model_client_check->execute("SELECT",false,"","client_id='".$clientId."'","id desc limit 0,1");
			if(count($checkClient)<=0) {
				$message=array("message"=>"Something Gone Wrong.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model_client=$this->app->load_model("client_logistic_assign");
			$obj_model_client->execute("DELETE",false,"","id='".$checkClient[0]['id']."'");
			
			$data_t=array();
			$data_t['client_logistic_assign_id ']=$checkClient[0]['id'];
			$data_t['client_id']=$clientId;
			$data_t['logistic_manager_employee_id']=$checkClient[0]['logistic_manager_employee_id'];
			$data_t['employee_id']=$checkClient[0]['employee_id'];
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['action']="Reject";
			$data_t['title']="Logistic Manager Rejected";
			$data_t['remark']=$remark;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_logistic_assign_history");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("INSERT",false,"","");

			$message=array("message"=>"Logistic Person Approved.","msgCode"=>"1");
		}
		else if($action=='BulkAssign')
		{
			$bulkClientIds=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("bulkClientIds"));
			if($bulkClientIds=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
			$client_ids=[];
			$bulkClientId=explode(',',$this->app->getPostVar('bulkClientIds'));
			foreach($bulkClientId as $item) {
				if($this->app->utility->decrypt($item)!=0){
					array_push($client_ids,$this->app->utility->decrypt($item));
				}
			}

			foreach($client_ids as $client)
			{
				$data_t=array();
				$data_t['client_id']=$client;
				$data_t['employee_id']=$employeeAssignID;
				$data_t['assign_by_employee_id']=$employeeID;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$obj_model_client=$this->app->load_model("client_logistic_assign");
				$obj_model_client->map_fields($data_t);
				$obj_model_client->execute("INSERT");
				
				$data_t=array();
				$data_t['client_id']=$client;
				$data_t['employee_id']=$employeeAssignID;
				$data_t['assign_by_employee_id']=$employeeID;
				$data_t['action']="Assign";
				$data_t['created_at']=date("Y-m-d H:i:s");
				$obj_model_client=$this->app->load_model("client_logistic_assign_history");
				$obj_model_client->map_fields($data_t);
				$obj_model_client->execute("INSERT",false,"","");
			}
			$message=array("message"=>"Logistic Person Assigned.","msgCode"=>"1");
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