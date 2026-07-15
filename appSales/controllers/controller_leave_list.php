<?
class _leave_list extends controller {
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
		

		$whereCond="employee_leave.id!=''";
		if($employeeID!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (employee_leave.reason LIKE '%$search%')";
			}
			if($status!='') {
				$whereCond.=" and employee_leave.status='".$status."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS Pending, COUNT(CASE WHEN status = 'Approved' THEN 1 END) AS Approved, COUNT(CASE WHEN status = 'Reject' THEN 1 END) AS Reject FROM employee_leave";
			
			$obj_model_task = $this->app->load_model("employee_leave");
			$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$statusList[]=["key"=>"Pending","value"=>"Pending (".$taskStatusResult[0]['Pending'].")"];
			$statusList[]=["key"=>"Approved","value"=>"In Progress (".$taskStatusResult[0]['Approved'].")"];
			$statusList[]=["key"=>"Reject","value"=>"Completed (".$taskStatusResult[0]['Reject'].")"];
			//count end

			$obj_model_task = $this->app->load_model("employee_leave");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_leave.id desc");
			$count=count($task);

			$limit=10; 
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["statusList"=>$statusList];
				$message=array("message"=>"No Task Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("employee_leave");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_task->join_table(["employee"=>"employeeR"], "left", array(), array("update_by_employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_leave.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
				$employeeRImage=$this->app->utility->get_image_url($item["employeeR_image"],'employee','large');

				$editDeleteBtn='No';
				$approveRejectBtn='No';
				if($employeeID==$item['employee_id'] && $item['status']=='Pending'){
					$editDeleteBtn='Yes';
				}
				if($loginAs=='Logistics Manager' && $item['status']=='Pending'){
					$approveRejectBtn='Yes';
				}
				$id=$this->app->utility->encrypt($item['id']);

				if($item['status']=='Pending') {
					$update_remark='Wait till logistic manager approved.';
				} else if($item['status']=='Approved') {
					$update_remark='Leave Approved.';
				} else {
					$update_remark=$item['update_remark'];
				}
				

				$leaveList[]=array(
				"id"=>$id,
				"number"=>'#'.$item['id'],
				"status"=>$item['status'],
				"textStatusColor"=>"#d1e7dd",
				"textStatusBgColor"=>"#0f5031",
				"leave_start"=>date('d-m-Y', strtotime($item['leave_start'])),
				"leave_end"=>date('d-m-Y', strtotime($item['leave_end'])),
				"reason"=>$item['reason'],
				"employee_image"=>$employeeImage,
				"employee_name"=>$employeeID==$item['employee_id']?'':$item['employee_name'],
				"employee_designation"=>$item['employee_name'],
				"employee_mobile"=>$item['employee_name'],
				"requested_date"=>date('d-m-Y', strtotime($item['created_at'])),
				"update_by_employee_image"=>$employeeRImage,
				"update_by_employee_name"=>$item['status']=='Pending'?'':$item['employee_name'],
				"update_by_employee_designation"=>$item['employee_name'],
				"update_by_mobile"=>$item['employee_mobile'],
				"update_remark"=>$update_remark,
				"updated_date"=>date('d-m-Y', strtotime($item['status_updated_at'])),
				"editDeleteBtn"=>$editDeleteBtn,
				"approveRejectBtn"=>$approveRejectBtn,
				);
			}
			$result=["leaveList"=>$leaveList,"statusList"=>$statusList];
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