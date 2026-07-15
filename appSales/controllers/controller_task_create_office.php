<?
class _task_create_office extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$taskCheckInDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskCheckInDate'));
		$taskCheckInTime=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskCheckInTime'));
		$taskCheckOutTime=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskCheckOutTime'));
		$taskRemark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('taskRemark'));

		if($employeeID=='' || $taskCheckInDate=='' || $taskRemark=='') {
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($employeeID!='')
		{
			//insert manual task
			$data_t=array();
			$data_t['employee_id']=$employeeID;
			$data_t['check_in']=date('Y-m-d', strtotime($taskCheckInDate)).' '.date('H:i:s', strtotime($taskCheckInTime));
			$data_t['check_out']=date('Y-m-d', strtotime($taskCheckInDate)).' '.date('H:i:s', strtotime($taskCheckOutTime));
			$data_t['task_remark']=$taskRemark;
			$data_t['device_type']=$deviceType;
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$data_t['created_at']=date("Y-m-d H:i:s");
			$data_t['updated_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("employee_task_office");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("INSERT",false,"","");
			
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