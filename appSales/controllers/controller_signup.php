<?
class _signup extends controller{
	function init(){
	}
	function onload()
	{
		$userName=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('userName'));
		$userEmail=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('userEmail'));
		$userMobile=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('userMobile'));
		$userPass=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('userPass'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		
		if($userName!='' && $userPass!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","email='".$userEmail."' or mobile='".$userMobile."'");

			if(count($employee)>0)
			{
				$message=array("message"=>"Your account already exist. Please Login","msgcode"=>"0");
				$response=$message;
				$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
				$final_response=$this->app->utility->indent($opt);
				echo $final_response; exit;
			}
 
			$data_t=array();		
			$data_t['name']=$userName;
			$data_t['email']=$userEmail;
			$data_t['mobile']=$userMobile;
			$data_t['login_password']=$userPass;
			$data_t['created_at']=date('Y-m-d H:i:s');
			$data_t['updated_at']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_signup");
			$obj_model_employee->map_fields($data_t);
			$ins=$obj_model_employee->execute("INSERT");

			$userType='Guest';
			$details[]=array("userType"=>$userType,"employeeID"=>$this->app->utility->encrypt($ins),"employeeName"=>$userName,"employeeEmail"=>$userEmail,"employeePhone"=>$userMobile,"employeeImage"=>'',"employeeDesignation"=>'',"employeeCode"=>'');
			$result=["detail"=>$details];
			$message=array("message"=>"Login Successfully.","msgCode"=>"1","result"=>$result);
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