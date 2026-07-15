<?
class _leave_logistic_list extends controller {
	function init() {
	}

	function onload() 
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		//$page=!empty($page)?(int)$page:0;
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		$status=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("status"));
		$loginAs=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("loginAs"));
		$leaveDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("leaveDate"));

		if($page==1){
			$message=array("message"=>"No Data Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt);
			exit;
		}
		
		if($employeeID!='' && $deviceType!='' && $leaveDate!='')
		{
			//check logistic manager
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$employeeID."'");

			if(count($employee)<=0 || $employee[0]['master_designation_name']!='Logistics Manager'){
				$message=array("message"=>"Login as Logistics Manager.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt);
				exit;
			}

			//find logistic employee of that logistic person
			$obj_model_employee = $this->app->load_model("employee");
			$logisticEmployee = $obj_model_employee->execute("SELECT",false,"","reporting_employee_lms_id ='".$employee[0]['lms_employee_id']."'");
			if(count($employee)<=0){
				$message=array("message"=>"No Data Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt);
				exit;
			}
			$logisticEmployeeIds=array_column($logisticEmployee, 'id');
			$logisticEmployeeIdsString=implode(',',$logisticEmployeeIds);

			//get employee who are on leave
			$whereCond="employee_leave.status='Approved' and FIND_IN_SET(`employee_leave`.`employee_id`,'".$logisticEmployeeIdsString."') and STR_TO_DATE('$leaveDate', '%d-%m-%Y')>=leave_start and STR_TO_DATE('$leaveDate', '%d-%m-%Y')<=leave_end";
			$obj_model_task = $this->app->load_model("employee_leave");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_leave.id desc");

			foreach($task as $item)
			{
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
		
				$id=$this->app->utility->encrypt($item['id']);
				$employeeID=$this->app->utility->encrypt($item['employee_id']);

				$obj_model = $this->app->load_model("client_logistic_assign");
				$det = $obj_model->execute("SELECT",false,"SELECT count(id) as total from client_logistic_assign where employee_id='".$item['employee_id']."' and request_status='Active'");

				$leaveList[]=array(
				"id"=>$id,
				"leave_start"=>date('d-m-Y', strtotime($item['leave_start'])),
				"leave_end"=>date('d-m-Y', strtotime($item['leave_end'])),
				"total_client"=>$det[0]['total'],
				"employeeID"=>$employeeID,
				"employee_image"=>$employeeImage,
				"employee_name"=>$item['employee_name'],
				"employee_designation"=>$item['employee_lms_employee_code'],
				"employee_mobile"=>$item['employee_mobile']
				);
			}

			$countList[]=["heading"=>"Logistic on leave","value"=>count($leaveList)];
			
			$result=["leaveEmployeeList"=>$leaveList,"countList"=>$countList];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
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