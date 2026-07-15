<?
class _hub_dashboard extends controller{
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
		
		if($employeeID!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","(id='".$employeeID."') and status='Active'");

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

				//employee_sample_pickup 
				$obj_model_employee_sample_pickup = $this->app->load_model("employee_sample_pickup");
				$sample_pickup = $obj_model_employee_sample_pickup->execute("SELECT",false,"SELECT sum(sample_count) as sum_collect,sum(payment_amount) as sum_payment FROM `employee_sample_pickup` left join employee_sample_pickup_hub_data on employee_sample_pickup_hub_data.employee_sample_pickup_id=employee_sample_pickup.id WHERE date(employee_sample_pickup_hub_data.created_at)=curdate() and received_employee_id='".$employeeID."'");
				$today[]=array("label"=>'Payment Collect',"value"=>$sample_pickup[0]['sum_collect']>0?$sample_pickup[0]['sum_collect']:0,"key"=>'',"bgColor"=>'#3995DB');
				$today[]=array("label"=>'Sample Collect',"value"=>$sample_pickup[0]['sum_payment']>0?$sample_pickup[0]['sum_payment']:0,"key"=>'Active',"bgColor"=>'#FC5D20');

				//
				$todayDay=date('D');
				$obj_model_client = $this->app->load_model("client");
				$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
				$obj_model_client->join_table("client_logistic_assign", "left", array("employee_id"), array("id"=>"client_id"));
				$client = $obj_model_client->execute("SELECT",false,"","client_logistic_assign.employee_id='".$employeeID."' and client.status='Active'");
				$totalTodays=0;
				foreach($client as $item)
				{
					$days=$item['client_detail_sample_pickup']!=''?explode(',',$item['client_detail_sample_pickup']):[];
					if(in_array($todayDay,$days))
					{
						$totalTodays++;
					}
				}

				$todayDate=date('d-m-y');
				$obj_model = $this->app->load_model("employee_sample_pickup");
				$client = $obj_model->execute("SELECT",false,"SELECT count(id) as total from employee_sample_pickup where employee_id='".$employeeID."' and pickup_date='".$todayDate."'");
				$CompletedPickup=$client[0]['total']>0?$client[0]['total']:0;

				$today[]=array("label"=>'Total Pickup',"value"=>$totalTodays,"key"=>'',"bgColor"=>'#3995DB');
				$today[]=array("label"=>'Completed',"value"=>$CompletedPickup,"key"=>'Active',"bgColor"=>'#FC5D20');

				//for my client
				$client=[];
				
				
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

				
				$journeyStart='Yes';
				$journeyEnd='Yes';
				$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
				$rs_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"","employee_daily_journey.employee_id='".$employeeID."' and journey_date='".date('Y-m-d')."'","employee_daily_journey.id desc");
				$journeyStart=count($rs_employee_daily_journey)>0?'No':'Yes';
				$journeyEnd=(count($rs_employee_daily_journey)>0 && $rs_employee_daily_journey[0]['end_datetime']==null && $journeyStart='No')?'Yes':'No';
				
				$journeyView=array("view"=>'Yes',"journey_start"=>$journeyStart,"journey_end"=>$journeyEnd);


				$result=["today"=>$today,"clientCard"=>$client??[],"attendanceView"=>$attendanceView,"journeyView"=>$journeyView,"changePasswordAlert"=>$changePasswordAlert,"appUpdateForcefully"=>$appUpdateForcefully,"appUpdateMsg"=>$appUpdateMsg];
				$message=array("message"=>"Login Successfully.","msgCode"=>"1","result"=>$result);
			}
			else
			{
				$message=array("message"=>"Phone no is not registered.","msgCode"=>"2");
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