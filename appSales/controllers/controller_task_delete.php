<?
class _task_delete extends controller {
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

		if($employeeID!='' && $employeePhone!='' && $deviceType!='' && $taskId!='')
		{
			$data_t=array();
			$data_t['status']='Trash';
			$obj_model_employee=$this->app->load_model("employee_task_master");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$taskId."' and employee_primary_id='".$employeeID."'");

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
}
?>