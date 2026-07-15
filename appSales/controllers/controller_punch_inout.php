<?
class _punch_inout extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action")); //punchIn, punchOut

		if($employeeID=='' || $action=='')
		{
			$message=array("message"=>"Data is missing.","msgcode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$obj_model_employee = $this->app->load_model("employee");
		$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."' and employee.status!='Trash'","employee.id desc");
		if(count($employee)<=0) { 
			$message=array("message"=>"No Task Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		
		if($action=='punchIn')
		{
			$obj_model_punch_inout = $this->app->load_model("employee_punch_inout");
			$punch_inout = $obj_model_punch_inout->execute("SELECT",false,"","employee_punch_inout.employee_id='".$employeeID."' and punch_date='".date('Y-m-d')."'","employee_punch_inout.id desc");
			if(count($punch_inout)>0) { 
				$message=array("message"=>"You are already punchIn.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			if($_FILES['punchInPhoto']['name']!='')
			{
				$employee_photo=$this->app->utility->resize_single_image($_FILES['punchInPhoto']['name'],$_FILES['punchInPhoto']['tmp_name'],$this->app->get_user_config("punch"),'500');
				$data_t['employee_photo']= $employee_photo;
				$this->app->utility->remove_uploaded_file();
			}			
			$data_t['employee_id']=$employeeID;
			$data_t['punch_date']=date('Y-m-d');
			$data_t['punchin_datetime']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_punch_inout");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			$message=array("message"=>"PunchIn Successfully.","msgCode"=>"1");
		}
		else if($action=='punchOut')
		{
			$obj_model_punch_inout = $this->app->load_model("employee_punch_inout");
			$punch_inout = $obj_model_punch_inout->execute("SELECT",false,"","employee_punch_inout.employee_id='".$employeeID."' and punch_date='".date('Y-m-d')."'","employee_punch_inout.id desc");
			
			if(count($punch_inout)>0 && $punch_inout[0]['punchout_datetime']==null)
			{
				$data_t=array();
				$data_t['punchout_datetime']=date('Y-m-d H:i:s');
				$obj_model_employee=$this->app->load_model("employee_punch_inout");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("UPDATE",false,"","id='".$punch_inout[0]['id']."'");

				$message=array("message"=>"PunchIn Successfully.","msgCode"=>"1");
			} else {
				$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
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