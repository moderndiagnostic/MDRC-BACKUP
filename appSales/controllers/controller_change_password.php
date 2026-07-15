<?
class _change_password extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$oldPassword=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("oldPassword"));
		$newPassword=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("newPassword"));
		
		if($employeeID!='' && $employeePhone!='' && $deviceType!='' && $oldPassword!='' && $newPassword!='signIn')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."' and mobile='".$employeePhone."' and login_password='".$oldPassword."'");

			if(count($employee)>0)
			{
				if($employee[0]['status']!='Active')
				{
					$message=array("message"=>"Your Account is Disable by Team. Contact Us","msgcode"=>"0");
					$response=$message;
					$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
					$final_response=$this->app->utility->indent($opt);
					echo $final_response; exit;
				}

				if($oldPassword==$newPassword)
				{
					$message=array("message"=>"Old Password and New password should not same.","msgcode"=>"0");
					$response=$message;
					$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
					$final_response=$this->app->utility->indent($opt);
					echo $final_response; exit;
				}


				$data_t=array();
				$data_t['login_password']=$newPassword;
				$obj_model_employee=$this->app->load_model("employee");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("UPDATE",false,"","id='".$employeeID."' and mobile='".$employeePhone."'");

				$message=array("message"=>"Password Reset Successfully.","msgCode"=>"1");
			}
			else
			{
				$message=array("message"=>"Please Enter Correct old Password.","msgCode"=>"0");
			}
			
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