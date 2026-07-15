<?
class _task_office_list extends controller {
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
		$taskStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("taskStatus"));

		$whereCond=' employee_task_office.status!="Trash"';
		if($employeeID!='' && $deviceType!='')
		{
			$whereCond.=" and (employee_task_office.employee_id='".$employeeID."')";
			if($search!='') {
				$whereCond.=" and (employee_task_office.task_remark LIKE '%$search%')";
			}
			if($taskStatus!='') {
				$whereCond.=" and employee_task_office.status='".$taskStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN status != '' THEN 1 END) AS AllCount, COUNT(CASE WHEN status = 'Active' THEN 1 END) AS Active, COUNT(CASE WHEN status = 'Inprogress' THEN 1 END) AS Inprogress, COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS Completed, COUNT(CASE WHEN status = 'Canceled' THEN 1 END) AS Canceled FROM employee_task_office where employee_id='".$employeeID."'";
			
			$obj_model_task = $this->app->load_model("employee_task_office");
			$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$taskStatusList[]=["key"=>"","value"=>"All (".$taskStatusResult[0]['AllCount'].")"];
			$taskStatusList[]=["key"=>"Completed","value"=>"Completed (".$taskStatusResult[0]['Completed'].")"];
			
			//count end

			$obj_model_task = $this->app->load_model("employee_task_office");
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_task_office.id desc");
			$count=count($task);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["taskStatus"=>$taskStatusList];
				$message=array("message"=>"No Task Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("employee_task_office");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_task_office.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');

				$officeTaskList[]=array(
					"id"=>$id,
					"number"=>'#'.$item['id'],
					"status"=>$item['status'],
					"image"=>$employeeImage,
					"name"=>$item['employee_name'],
					"designation"=>'designation',
					"checkIn"=>date('d-m-Y h:i A', strtotime($item['check_in'])),
					"checkOut"=>date('d-m-Y h:i A', strtotime($item['check_out'])),
					"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
					"latitude"=>$item['latitude'],
					"longitude"=>$item['longitude'],
					"remark"=>$item['task_remark'],
					"hideEmployee"=>'No',
					"textStatusColor"=>"#d1e7dd",
					"textStatusBgColor"=>"#0f5031"
				);
			}
			$result=["officeTaskList"=>$officeTaskList,"taskStatus"=>$taskStatusList];
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