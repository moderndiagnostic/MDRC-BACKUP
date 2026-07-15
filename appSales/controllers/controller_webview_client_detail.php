<?
class _webview_client_detail extends controller {
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getGetVar('employeeID'));
		$selectEmployeeId=empty($this->app->getGetVar('selectEmployeeId'))?$employeeID:$this->app->getGetVar('selectEmployeeId');

		$employeeID=$this->app->utility->decrypt($employeeID);
		$selectEmployeeId=$this->app->utility->decrypt($selectEmployeeId);

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getGetVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);
		$whereCond='';
		if($clientID!=''){
		
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$selectEmployeeId."'","employee.id desc limit 0,1");

			$whereCond.="and client.id='".$clientID."'";

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$clientDetail = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc");
			$client=$clientDetail[0];

			$sealsPerson=$this->getSealsPersonList($client,$employee);

			$obj_model_task = $this->app->load_model("employee_task_master");
			$task = $obj_model_task->execute("SELECT",false,"SELECT COUNT(CASE WHEN employee_task_master_detail.meeting_client_meet = 'Yes' THEN 1 ELSE NULL END) AS meetCount,COUNT(CASE WHEN employee_task_master.id IS NOT NULL THEN 1 ELSE NULL END) AS count FROM employee_task_master LEFT JOIN employee_task_master_detail ON employee_task_master.id = employee_task_master_detail.employee_task_master_id WHERE employee_task_master.client_id='".$client['id']."' and employee_primary_id='".$sealsPerson['id']."'");

			$logisticPerson=$this->getLogisticPersonList($client,$employee);

			$obj_model_sample = $this->app->load_model("employee_sample_pickup");
			$sample = $obj_model_sample->execute("SELECT",false,"SELECT COUNT(CASE WHEN employee_sample_pickup.collect_sample='Yes' THEN 1 ELSE NULL END) AS collectSample,SUM(employee_sample_pickup.payment_amount) AS totalAmount,COUNT(CASE WHEN employee_sample_pickup.id IS NOT NUll THEN 1 ELSE NULL END) AS totalVisit FROM employee_sample_pickup WHERE employee_sample_pickup.client_id='".$client['id']."' and employee_sample_pickup.employee_id='".$logisticPerson['id']."'");
			
			$this->app->assign("client", $client);
			$this->app->assign("task", $task[0]);
			$this->app->assign("sample", $sample[0]);
			$this->app->assign("seals_person",$sealsPerson);
			$this->app->assign("logistic_person",$logisticPerson);
		}
		else
		{
			$this->app->redirect("index.php?view=webview_my_team&webview=close");
			exit;
		}
	}

	function getSealsPersonList($client,$loginEmployee)
	{
		$personList=[];
		if($client['lms_employee_id']>0) {
			//get sales person details
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employeeDetail = $obj_model_employee->execute("SELECT",false,"","lms_employee_id='".$client['lms_employee_id']."'","employee.id desc limit 0,1");
		}

		if($client['client_detail_added_by_employee_id']>0 && $client["client_status"]!='Client') {
			//get sales person details
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$client['client_detail_added_by_employee_id']."'","employee.id desc limit 0,1");
		}
		return $employeeDetail[0];
	}
	function getLogisticPersonList($client,$loginEmployee)
	{
		$personList=[];
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$client['id']."'","id desc limit 0,1");
		
		if(count($logistic_assign)>0) {

			if($logistic_assign[0]['request_status']=='Active') {
				//get sales person details
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$logistic_assign[0]['employee_id']."'","employee.id desc limit 0,1");
			}
		}
		return $employeeDetail[0];
	}
}
?>