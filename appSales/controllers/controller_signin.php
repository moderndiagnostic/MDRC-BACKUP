<?
class _signin extends controller{
	function init(){
	}
	function onload()
	{
		$userName=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('userName'));
		$userPass=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('userPass'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));
		
		if($userName!='' && $userPass!='' && $deviceType!='' && $action=='signIn')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","(email='".$userName."' or mobile='".$userName."' or lms_employee_code='".$userName."') and login_password='".$userPass."'");

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

				$employeeID=$this->app->utility->encrypt($employee[0]['id']);
				$employeePhone=$employee[0]['mobile'];
				$employeeName=$employee[0]['name'];
				$employeeEmail=$employee[0]['email'];
				$employeeDesignation=$employee[0]['master_designation_name'];

				if($employee[0]['image']!='')
				{
					$employeeImage=SERVER_ROOT.'/uploads/employee/'.$employee[0]['image'];
				}
				else
				{
					$employeeImage=SERVER_ROOT.'/uploads/profile.png';
				}
				$notManagement=['Sales','Logistics Manager','Logistics','Management'];
				/* if(!in_array($employeeDesignation,$notManagement))
				{
					$employeeDesignation='Management';
				} */
				$userType='User';
				$details[]=array("userType"=>$userType,"employeeID"=>$employeeID,"employeeName"=>$employeeName,"employeeEmail"=>$employeeEmail,"employeePhone"=>$employeePhone,"employeeImage"=>$employeeImage,"employeeDesignation"=>$employeeDesignation,"employeeCode"=>$employee[0]['lms_employee_code']);
				$result=["detail"=>$details];
				$message=array("message"=>"Login Successfully.","msgCode"=>"1","result"=>$result);
			}
			else
			{
				$obj_model_employee = $this->app->load_model("employee_signup");
				$employee = $obj_model_employee->execute("SELECT",false,"","(email='".$userName."' or mobile='".$userName."') and login_password='".$userPass."'");
				if(count($employee)>0)
				{
					$employeeID=$this->app->utility->encrypt($employee[0]['id']);
					$employeePhone=$employee[0]['mobile'];
					$employeeName=$employee[0]['name'];
					$employeeEmail=$employee[0]['email'];
					
					$userType='Guest';
					$details[]=array("userType"=>$userType,"employeeID"=>$employeeID,"employeeName"=>$employeeName,"employeeEmail"=>$employeeEmail,"employeePhone"=>$employeePhone,"employeeImage"=>'',"employeeDesignation"=>'',"employeeCode"=>'');
					$result=["detail"=>$details];
					$message=array("message"=>"Login Successfully.","msgCode"=>"1","result"=>$result);
				}else{
					$message=array("message"=>"Employee Code not registered.","msgCode"=>"0");
				}
			}
			
		}
		else if($userName!='' && $deviceType!='' && $action=='forgetPassword')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","(email='".$userName."' or phone='".$userName."')");

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

				$message=array("message"=>"Password Sent Successfully.","msgCode"=>"1");
			}
			else
			{
				$message=array("message"=>"Phone no is not registered.","msgCode"=>"0");
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