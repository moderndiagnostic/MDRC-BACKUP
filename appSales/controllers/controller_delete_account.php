<?
class _delete_account extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		
		if($employeeID!='' && $employeePhone!='' && $deviceType!='')
		{
			// $obj_model_employee = $this->app->load_model("employee");
			// $employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."' and mobile='".$employeePhone."'");

			// if(count($employee)>0)
			// {
				$message=array("message"=>"Account Deleted Successfully.","msgCode"=>"1");
			// }
			// else
			// {
			// 	$message=array("message"=>"Account Not Found.","msgCode"=>"0");
			// }
			
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