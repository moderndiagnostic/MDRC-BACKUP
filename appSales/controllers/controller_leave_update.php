<?
class _leave_update extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$leaveID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("leaveID"));
		$leaveID=$leaveID==''?'':$this->app->utility->decrypt($leaveID);

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action")); //checkIn, checkOut, meeting

		$fromDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("fromDate"));
		$toDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("toDate"));
		$remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("remark"));

		if($employeeID=='' || $action=='')
		{
			$message=array("message"=>"Data is missing.","msgcode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($leaveID!=''){
			$obj_model_leave = $this->app->load_model("employee_leave");
			//$obj_model_employee_task->join_table("employee", "left", array(), array("id"=>"employee_task_master_id"));
			$leave = $obj_model_leave->execute("SELECT",false,"","employee_leave.status!='Trash' and employee_leave.id='".$leaveID."'");
			if(count($leave)<=0) { 
				$message=array("message"=>"No Leave Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
		}
		
		if($action=='addEdit' && $fromDate!='' && $toDate!='')
		{
			$data_t=array();		
			$data_t['employee_id']=$employeeID;
			$data_t['leave_start']=date('Y-m-d', strtotime($fromDate));
			$data_t['leave_end']=date('Y-m-d', strtotime($toDate));
			$data_t['reason']=$remark;
			$data_t['created_at']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_leave");
			$obj_model_employee->map_fields($data_t);
			if($leaveID==''){
				$obj_model_employee->execute("INSERT");

				//send push start
				$obj_model=$this->app->load_model("employee");
				$employee=$obj_model->execute("SELECT",false,"","id='".$employeeID."'");
				if($employee[0]['reporting_employee_lms_id']>0)
				{
					$obj_model_employee=$this->app->load_model("employee");
					$managerDetail=$obj_model_employee->execute("SELECT",false,"","lms_employee_id='".$employee[0]['reporting_employee_lms_id']."'");
					
					$data['employee_ids']=$managerDetail[0]['id'];
					$title='Leave Request by '.$employee[0]['name'].' on '.$fromDate.' to '.$toDate;
					$message=$remark;
					$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'LogisticLeaveRequestListActivity');
					$this->app->utility->send_push($data);

					//notification data insert
					$data_t=array();
					$data_t['noti_type']='Leave Request';
					$data_t['title']=$title;
					$data_t['description']=$message;
					$data_t['employee_ids']=$data['employee_ids'];
					$data_t['table_id']=0;
					$data_t['created_by']=$employeeID;
					$data_t['created_at']=date("Y-m-d H:i:s");
					$obj_model_employee_task_master=$this->app->load_model("notifications");
					$obj_model_employee_task_master->map_fields($data_t);
					$obj_model_employee_task_master->execute("INSERT");
				}
				//send push end
				
			} else {
				$obj_model_employee->execute("UPDATE",false,"","id='".$leaveID."'");
			}

			$message=array("message"=>"Leave Updated.","msgCode"=>"1");
		}
		else if($action=='delete' && $leaveID!='')
		{
			$obj_model_employee=$this->app->load_model("employee_leave");
			$obj_model_employee->execute("DELETE",false,"","id='".$leaveID."' and employee_id='".$employeeID."'");

			$message=array("message"=>"Leave Removed.","msgCode"=>"1");
		}
		else if($action=='reject' && $leaveID!='')
		{
			$data_t=array();		
			$data_t['status']='Reject';
			$data_t['update_by_employee_id']=$employeeID;
			$data_t['status_updated_at']=date("Y-m-d H:i:s");
			$data_t['update_remark']=$remark;
			$obj_model_employee=$this->app->load_model("employee_leave");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$leaveID."'");

			//send push start
			$obj_model_employee=$this->app->load_model("employee_leave");
			$leaveDetail=$obj_model_employee->execute("SELECT",false,"","id='".$leaveID."'");
			
			$data['employee_ids']=$leaveDetail[0]['employee_id'];
			$title='Leave Reject by Manager of '.date('d-m-Y', strtotime($leaveDetail[0]['leave_start'])).' to '.date('d-m-Y', strtotime($leaveDetail[0]['leave_end']));
			$message=$remark;
			$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'LogisticLeaveRequestListActivity');
			$this->app->utility->send_push($data);

			//notification data insert
			$data_t=array();
			$data_t['noti_type']='Leave Request';
			$data_t['title']=$title;
			$data_t['description']=$message;
			$data_t['employee_ids']=$data['employee_ids'];
			$data_t['table_id']=0;
			$data_t['created_by']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("notifications");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("INSERT");
			//send push end

			$message=array("message"=>"Leave Updated.","msgCode"=>"1");
		}
		else if($action=='approve' && $leaveID!='')
		{
			$data_t=array();		
			$data_t['status']='Approved';
			$data_t['update_by_employee_id']=$employeeID;
			$data_t['status_updated_at']=date("Y-m-d H:i:s");
			$obj_model_employee=$this->app->load_model("employee_leave");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$leaveID."'");

			//send push start
			$obj_model_employee=$this->app->load_model("employee_leave");
			$leaveDetail=$obj_model_employee->execute("SELECT",false,"","id='".$leaveID."'");
			
			$data['employee_ids']=$leaveDetail[0]['employee_id'];
			$title='Leave Approved Manager';
			$message='Leave of '.date('d-m-Y', strtotime($leaveDetail[0]['leave_start'])).' to '.date('d-m-Y', strtotime($leaveDetail[0]['leave_end']));
			$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'LogisticLeaveRequestListActivity');
			$this->app->utility->send_push($data);

			//notification data insert
			$data_t=array();
			$data_t['noti_type']='Leave Request';
			$data_t['title']=$title;
			$data_t['description']=$message;
			$data_t['employee_ids']=$data['employee_ids'];
			$data_t['table_id']=0;
			$data_t['created_by']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("notifications");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("INSERT");
			//send push end

			$message=array("message"=>"Leave Removed.","msgCode"=>"1");
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