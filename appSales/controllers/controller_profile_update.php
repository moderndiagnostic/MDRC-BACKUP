<?
class _profile_update extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		
		if($employeeID!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'");

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

				if($_FILES['profilePhoto']['name']!='')
				{
					$data_t=array();

					$profilePhoto=$this->app->utility->resize_single_image($_FILES['profilePhoto']['name'],$_FILES['profilePhoto']['tmp_name'],$this->app->get_user_config("employee"),'800');
					$data_t['image']= $profilePhoto;
					$this->app->utility->remove_uploaded_file();

					$obj_model_employee=$this->app->load_model("employee");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$employeeID."'");

					$employeeImage=SERVER_ROOT.'/uploads/employee/'.$profilePhoto;
					$result=["employeeImage"=>$employeeImage];
					$message=array("message"=>"Profile Pic Changed.","msgCode"=>"1","result"=>$result);
				}
				else
				{
					$message=array("message"=>"Please Upload Profile Pic.","msgCode"=>"0");
				}
			}
			else
			{
				$message=array("message"=>"Data not found.","msgCode"=>"0");
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