<?
class _dashboard extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		$fcmToken=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("fcmToken"));
		$deviceId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceId"));
		$appVersion=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("appVersion"));
		
		if($employeeID!='' && $deviceType!='' && $appVersion!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","(id='".$employeeID."' and mobile='".$employeePhone."') and status='Active'");

			if(count($employee)>0)
			{
				if($employee[0]['status']!='Active')
				{
					$message=array("message"=>"Your Account is Disable by Team. Contact Us","msgcode"=>"2");
					$response=$message;
					$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
					$final_response=$this->app->utility->indent($opt);
					echo $final_response; exit;
				}


				if($fcmToken!='') {
					$data_t=array();
					$data_t['fcm_token']=$fcmToken;
					$data_t['app_version']=$appVersion;
					$obj_model_employee=$this->app->load_model("employee");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$employeeID."'");	
				}	

				//for task count
				$query="SELECT COUNT(CASE WHEN status != '' THEN 1 END) AS AllCount, COUNT(CASE WHEN status = 'Active' THEN 1 END) AS Active, COUNT(CASE WHEN status = 'Inprogress' THEN 1 END) AS Inprogress, COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS Completed, COUNT(CASE WHEN status = 'Canceled' THEN 1 END) AS Canceled FROM employee_task_master where employee_primary_id='".$employeeID."' and status!='Trash'";
				$obj_model_task = $this->app->load_model("employee_task_master");
				$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
				
				$today[]=array("label"=>'Total Task',"value"=>$taskStatusResult[0]['AllCount'],"key"=>'',"bgColor"=>'#3995DB');
				$today[]=array("label"=>'Active',"value"=>$taskStatusResult[0]['Active'],"key"=>'Active',"bgColor"=>'#FC5D20');
				$today[]=array("label"=>'In Progress',"value"=>$taskStatusResult[0]['Inprogress'],"key"=>'Inprogress',"bgColor"=>'#0f5031');
				$today[]=array("label"=>'Completed',"value"=>$taskStatusResult[0]['Completed'],"key"=>'Completed',"bgColor"=>'#3BB537');

				//for my client
				$query="SELECT COUNT(CASE WHEN client.status != 'Trash' THEN 1 END) AS AllCount FROM client LEFT JOIN client_detail on client.id=client_detail.client_id where client.lms_employee_id='".$employee[0]['lms_employee_id']."' || client_detail.added_by_employee_id='".$employeeID."'";
				$obj_model_client = $this->app->load_model("client");
				$clientCountQuery = $obj_model_client->execute("SELECT",false,$query);

				$client[]=array("label"=>'Total',"value"=>0,"key"=>'',"bgColor"=>'#3995DB');
				$client[]=array("label"=>'My Client',"value"=>$clientCountQuery[0]['AllCount'],"key"=>'myClient',"bgColor"=>'#3BB537');
				$client[]=array("label"=>'Team Client',"value"=>0,"key"=>'Inprogress',"bgColor"=>'#0f5031');
				
				//for punching
				$punchIn='Yes';
				$punchOut='Yes';
				$obj_model_punch_inout = $this->app->load_model("employee_punch_inout");
				$punch_inout = $obj_model_punch_inout->execute("SELECT",false,"","employee_punch_inout.employee_id='".$employeeID."' and punch_date='".date('Y-m-d')."'","employee_punch_inout.id desc");
				$punchIn=count($punch_inout)>0?'No':'Yes';
				$punchOut=(count($punch_inout)>0 && $punch_inout[0]['punchout_datetime']==null && $punchIn='No')?'Yes':'No';
				
				$attendanceView=array("view"=>'Yes',"punchIn"=>$punchIn,"punchOut"=>$punchOut);

				//change password
				$changePasswordAlert=$employee[0]['login_password']=='Admin'?'Yes':'No';

				$appUpdateForcefully="No";
				$appUpdateMsg="Dear Customer, New Version of App is available, Please Update Now!";
				$appUpdateMsg="";

				if($deviceType=='adnroid')
				{
					if($appVersion<2.5){
						$appUpdateForcefully="Yes";
						$appUpdateMsg="Dear Customer, New Version of App is available, Please Update Now!";
					}
				}
				elseif($deviceType=='ios') {
					if($appVersion<1.8){
						$appUpdateForcefully="Yes";
						$appUpdateMsg="Dear Customer, New Version of App is available, Please Update Now!";
					}
				}

				$purposeList[]=["id"=>"Billing Discussion","name"=>"Billing Discussion"];
				$purposeList[]=["id"=>"Test Marketing","name"=>"Test Marketing"];
				$purposeList[]=["id"=>"CME Related","name"=>"CME Related"];
				$purposeList[]=["id"=>"Potential Client/Business","name"=>"Potential Client/Business"];
				$purposeList[]=["id"=>"Feedback Visit","name"=>"Feedback Visit"];
				$purposeList[]=["id"=>"Literature","name"=>"Literature"];
				

				$userType='User';
				$result=["userType"=>$userType,"today"=>$today,"clientCard"=>$client??[],"attendanceView"=>$attendanceView,"changePasswordAlert"=>$changePasswordAlert,"appUpdateForcefully"=>$appUpdateForcefully,"appUpdateMsg"=>$appUpdateMsg,"purposeList"=>$purposeList];
				$message=array("message"=>"Login Successfully.","msgCode"=>"1","result"=>$result);
			}
			else
			{
				$obj_model_employee = $this->app->load_model("employee_signup");
				$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'");
				if(count($employee)>0)
				{
					$userType='Guest';
					$result=["userType"=>$userType];
					$message=array("message"=>"guest","msgCode"=>"1","result"=>$result);
				}else{
					$message=array("message"=>"Phone no is not registered.","msgCode"=>"2");
				}
				
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