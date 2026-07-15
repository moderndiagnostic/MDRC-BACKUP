<?
class _employee_daily_journey extends controller
{
	function init()
	{
		###
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$km=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('km'));
		$latitude=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('latitude'));
		$longitude=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('longitude'));
		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));
		$journeyID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('journeyID'));

		## CHECK PRIMARY DATA MISSING
		if($employeeID=='' || $action=='')
		{
			$message=array("message"=>"Data is missing.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		## CHECK EMPLOYEE TASK ASSIGNED
		$obj_model_employee = $this->app->load_model("employee");
		$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."' and employee.status!='Trash'","employee.id desc");
		if(count($employee)<=0) { 
			$message=array("message"=>"No Task Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		
		if($action=='start_km')
		{
			## CHECK ALREADY STARTED
			$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
			$rs_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"","employee_daily_journey.employee_id='".$employeeID."'","employee_daily_journey.id desc");
			
			//if(count($rs_employee_daily_journey)>0 && (($rs_employee_daily_journey[0]['end_km'] == '' && $rs_employee_daily_journey[0]['start_km'] != '') || $rs_employee_daily_journey[0]['journey_date'] == date('Y-m-d')))
			if(count($rs_employee_daily_journey)>0 && $rs_employee_daily_journey[0]['journey_date'] == date('Y-m-d'))
			{
				$message=array("message"=>"You are already start journey.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			## CHECK BLANK VALUE
			if($km!='' && $latitude!='' && $longitude!='' && $_FILES['image']['name']!='')
			{
				## INSERT DATA IN PRIMARY TABLE
				$data_a=array();
				
				# UPLOAD IMAGE
				$employee_photo=$this->app->utility->resize_single_image($_FILES['image']['name'],$_FILES['image']['tmp_name'],$this->app->get_user_config("daily_journey"),'500');
				$data_a['start_image']= $employee_photo;
				
				$data_a['employee_id']=$employeeID;
				$data_a['journey_date']=date('Y-m-d');
				$data_a['start_datetime']=date('Y-m-d H:i:s');
				$data_a['start_km']=$km;
				$data_a['image_path']=SERVER_ROOT.'/uploads/daily_journey/';
				$data_a['start_latitude']=$latitude;
				$data_a['start_longitude']=$longitude;
				$data_a['status']='Running';
				$obj_model_journey=$this->app->load_model("employee_daily_journey");
				$obj_model_journey->map_fields($data_a);
				$journey_id = $obj_model_journey->execute("INSERT");

				## INSERT DATA IN DETAIL TABLE
				$data_b=array();
				$data_b['employee_daily_journey_id']=$journey_id;
				$obj_model_journey_detail=$this->app->load_model("employee_daily_journey_detail");
				$obj_model_journey_detail->map_fields($data_b);
				$obj_model_journey_detail->execute("INSERT");

				## INSERT DATA IN LOGS TABLE
				$data_c=array();
				$data_c['employee_daily_journey_id']=$journey_id;
				$data_c['employee_id']=$employeeID;
				$data_c['title']='Start Journey';
				$data_c['status']='Running';
				$obj_model_journey_logs=$this->app->load_model("employee_daily_journey_logs");
				$obj_model_journey_logs->map_fields($data_c);
				$obj_model_journey_logs->execute("INSERT");

				## SET RESPONCES
				$message=array("message"=>"Journey Start Successfully.","msgCode"=>"1");
			}
			else
			{
				$message=array("message"=>"Some Data is missing.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
		}
		elseif($action=='end_km')
		{
			$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
			$res_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"","employee_daily_journey.employee_id='".$employeeID."' and journey_date='".date('Y-m-d')."'","employee_daily_journey.id desc");

			$start_km = $res_employee_daily_journey[0]['start_km'];
			if($start_km > $km)
			{
				$message=array("message"=>"Not can enter end km less then start km.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
			$totalKm = $km-$start_km;

			if(count($res_employee_daily_journey)>0 && $res_employee_daily_journey[0]['end_km']=='')
			{
				if($km!='' && $longitude!='' && $latitude!='' && $_FILES['image']['name']!='')
				{
					## UPDATE IN PRIMARY TABLE
					$data_d=array();
					$employee_photo2=$this->app->utility->resize_single_image($_FILES['image']['name'],$_FILES['image']['tmp_name'],$this->app->get_user_config("daily_journey"),'500');
					$data_d['end_image']= $employee_photo2;
					$data_d['status']= 'Pending';
					$data_d['end_datetime']=date('Y-m-d H:i:s');
					$data_d['end_km']=$km;
					$data_d['total_km']=$totalKm;
					$data_d['end_latitude']=$latitude;
					$data_d['end_longitude']=$longitude;
					$obj_model_daily_journey=$this->app->load_model("employee_daily_journey");
					$obj_model_daily_journey->map_fields($data_d);
					$obj_model_daily_journey->execute("UPDATE",false,"","id='".$res_employee_daily_journey[0]['id']."'");

					## INSERT IN LOGS TABLE
					$data_e=array();
					$data_e['employee_daily_journey_id']=$res_employee_daily_journey[0]['id'];
					$data_e['title']='End Journey';
					$data_e['employee_id']=$employeeID;
					$data_e['status']='Pending';
					$obj_daily_journey_logs=$this->app->load_model("employee_daily_journey_logs");
					$obj_daily_journey_logs->map_fields($data_e);
					$obj_daily_journey_logs->execute("INSERT");

					$message=array("message"=>"Journey End Successfully.","msgCode"=>"1");
				}
				else
				{
					$message=array("message"=>"Some Data is missing.","msgCode"=>"0");
					$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
					echo $this->app->utility->indent($opt); exit;
				}
			} else {
				$message=array("message"=>"Your Journey Not Started. Try again...","msgCode"=>"0");
			}
		}
		elseif($action=='journeyUpdate')
		{
			## GET POST PARAMETER
			$type=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('type'));
			$remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('remark'));

			## CHECK
			if($type!='' && $journeyID!='')
			{
				$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
				$res_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"","id='".$journeyID."'");

				if(count($res_employee_daily_journey)>0)
				{
					## UPDATE IN DETAIL TABLE
					$data_h=array();

					$obj_employee = $this->app->load_model("employee");
					$rs_emp = $obj_employee->execute("SELECT",false,"","id='".$res_employee_daily_journey[0]['employee_id']."'");

					$obj_employee2 = $this->app->load_model("employee");
					$rs_emp2 = $obj_employee2->execute("SELECT",false,"","lms_employee_id='".$rs_emp[0]['reporting_employee_lms_id']."'");

					$obj_manager = $this->app->load_model("employee_journey_finance_submanager");
					$obj_manager->join_table("employee_journey_finance_manager", "left", array(), array("employee_journey_finance_manager_id"=>"id"));
					$data_manager = $obj_manager->execute("SELECT",false,"","employee_journey_finance_submanager.employee_id='".$rs_emp2[0]['id']."'");

					if(count($data_manager)>0)
					{
						if($data_manager[0]['employee_id'] == $employeeID)
						{
							## APPROVE BY MANAGER
							if($type == 'Approve'){
								$status='Approve By Manager';
							} else {
								$status='Reject By Manager';
							}
							$data_h['manager_employee_id']=$employeeID;
							$data_h['manager_datetime']=date('Y-m-d H:i:s');
							$data_h['manager_remark']=$remark;
						}
						elseif($data_manager[0]['employee_journey_finance_manager_employee_id'] == $employeeID)
						{
							## APPROVE BY FINANCE
							if($type == 'Approve'){
								$status='Approve By Finance';
							} else {
								$status='Reject By Finance';
							}
							$data_h['finance_employee_id']=$employeeID;
							$data_h['finance_datetime']=date('Y-m-d H:i:s');
							$data_h['finance_remark']=$remark;
						}
					}
					else
					{
						## APPROVE BY MANAGER
						if($type == 'Approve'){
							$status='Approve By Manager';
						} else {
							$status='Reject By Manager';
						}
						$data_h['manager_employee_id']=$employeeID;
						$data_h['manager_datetime']=date('Y-m-d H:i:s');
						$data_h['manager_remark']=$remark;
					}
										
					$obj_employee_daily_journey_detail=$this->app->load_model("employee_daily_journey_detail");
					$obj_employee_daily_journey_detail->map_fields($data_h);
					$obj_employee_daily_journey_detail->execute("UPDATE",false,"","employee_daily_journey_id='".$journeyID."'");

					## UPDATE IN PRIMARY TABLE
					$data_g=array();
					$data_g['status']=$status;
					$obj_employee_daily_journeyA=$this->app->load_model("employee_daily_journey");
					$obj_employee_daily_journeyA->map_fields($data_g);
					$obj_employee_daily_journeyA->execute("UPDATE",false,"","id='".$journeyID."'");

					## INSERT IN LOGS TABLE
					$data_e=array();
					$data_e['employee_daily_journey_id']=$res_employee_daily_journey[0]['id'];
					$data_e['title']='Journey '.$status;
					$data_e['employee_id']=$res_employee_daily_journey[0]['employee_id'];
					$data_e['status']=$status;
					$obj_daily_journey_logs=$this->app->load_model("employee_daily_journey_logs");
					$obj_daily_journey_logs->map_fields($data_e);
					$obj_daily_journey_logs->execute("INSERT");

					$message=array("message"=>"Journey Updated Successfully.","msgCode"=>"1");
				}
				else
				{
					$message=array("message"=>"Data is missing.","msgCode"=>"0");
					$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
					echo $this->app->utility->indent($opt); exit;
				}
			}
			else
			{
				$message=array("message"=>"Some Data is missing.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
		}
		elseif($action=='journeyModify')
		{
			$start_km=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('start_km'));
			$end_km=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('end_km'));

			$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
			$res_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"","id='".$journeyID."'");

			# LOGIN EMPLOYEE
			$obj_model_employeeL = $this->app->load_model("employee");
			$login_employee = $obj_model_employeeL->execute("SELECT",false,"","id='".$employeeID."'");

			# JOURNEY EMPLOYEE
			$obj_model_employeeJ = $this->app->load_model("employee");
			$journey_employeeB = $obj_model_employeeJ->execute("SELECT",false,"","id='".$res_employee_daily_journey[0]['employee_id']."'");

			$mainStatus = 'Approve By Finance';
			$title = 'Journey Approve By Finance';

			if($login_employee[0]['lms_employee_id'] == $journey_employeeB[0]['reporting_employee_lms_id'])
			{
				$mainStatus = 'Approve By Manager';
				$title = 'Journey Approve By Manager';
			}
			// echo $mainStatus; exit;
			if(!empty($start_km) && !empty($end_km) && !empty($journeyID))
			{
				if(count($res_employee_daily_journey)>0)
				{
					if($start_km < $end_km)
					{
						$total_km=$end_km-$start_km;
						$data_z=array();
						$data_z['start_km']=$start_km;
						$data_z['end_km']=$end_km;
						$data_z['total_km']=$total_km;
						$data_z['status']=$mainStatus;
						$obj_employee_daily_journeyZ=$this->app->load_model("employee_daily_journey");
						$obj_employee_daily_journeyZ->map_fields($data_z);
						$obj_employee_daily_journeyZ->execute("UPDATE",false,"","id='".$journeyID."'");

						if($login_employee[0]['lms_employee_id'] == $journey_employeeB[0]['reporting_employee_lms_id'])
						{
							$data_y=array();
							$data_y['manager_datetime']=date('Y-m-d H:i:s');
							$data_y['manager_employee_id']=$employeeID;
							$obj_employee_daily_journeyY=$this->app->load_model("employee_daily_journey_detail");
							$obj_employee_daily_journeyY->map_fields($data_y);
							$obj_employee_daily_journeyY->execute("UPDATE",false,"","employee_daily_journey_id='".$journeyID."'");
						}
						else
						{
							$data_y=array();
							$data_y['finance_datetime']=date('Y-m-d H:i:s');
							$data_y['finance_employee_id']=$employeeID;
							$obj_employee_daily_journeyY=$this->app->load_model("employee_daily_journey_detail");
							$obj_employee_daily_journeyY->map_fields($data_y);
							$obj_employee_daily_journeyY->execute("UPDATE",false,"","employee_daily_journey_id='".$journeyID."'");
						}

						## INSERT IN LOGS TABLE
						$data_x=array();
						$data_x['employee_daily_journey_id']=$journeyID;
						$data_x['title']=$title;
						$data_x['employee_id']=$res_employee_daily_journey[0]['employee_id'];
						$data_x['status']=$mainStatus;
						$obj_daily_journey_logsX=$this->app->load_model("employee_daily_journey_logs");
						$obj_daily_journey_logsX->map_fields($data_x);
						$obj_daily_journey_logsX->execute("INSERT");

						$message=array("message"=>"Journey Updated Successfully.","msgCode"=>"1");
					}
					else
					{
						$message=array("message"=>"Not can enter end km less then start km.","msgCode"=>"0");
						$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
						echo $this->app->utility->indent($opt); exit;
					}
				}
				else
				{
					$message=array("message"=>"Journey Not Found.","msgCode"=>"0");
					$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
					echo $this->app->utility->indent($opt); exit;
				}
			}
			else
			{
				$message=array("message"=>"Somthing Went Wrong.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
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