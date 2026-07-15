<?php
class _leave_logistic_assign_list extends controller {
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
				
		if($employeeID!='' && $deviceType!='' )
		{
			if($leaveDate!=''){
				$whereCond="employee_leave_assign_client.id!='' and STR_TO_DATE('$leaveDate', '%d-%m-%Y')=assign_date";
			}

			if($search!='') {
				//$whereCond.=" and (employee_leave_assign_client.reason LIKE '%$search%')";
			}
			if($status!='') {
				//$whereCond.=" and employee_leave_assign_client.status='".$status."'";
			}

			$obj_model_task = $this->app->load_model("employee_leave_assign_client");
			//$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_leave_assign_client.id desc");
			
			$count=count($task);

			$limit=10; 
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No data Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("employee_leave_assign_client");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_leave_assign_client.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
				$id=$this->app->utility->encrypt($item['id']);

				$leaveLogisticAssignClientList[]=array(
				"id"=>$id,
				"number"=>'#'.$item['id'],
				"assign_date"=>date('d-m-Y', strtotime($item['assign_date'])),
				"image"=>$employeeImage,
				"name"=>$item['employee_name'],
				"code"=>$item['employee_lms_employee_code'],
				"mobile"=>$item['employee_mobile'],
				"client_assign"=>count(explode(',',$item['client_ids']))
				);
			}
			$result=["leaveLogisticAssignClientList"=>$leaveLogisticAssignClientList];
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