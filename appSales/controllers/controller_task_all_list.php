<?
class _task_all_list extends controller {
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

		$whereCond='employee_task_master.status!="Trash" and employee_task_master.status!="Draft"';
		if($employeeID!='' && $deviceType!='')
		{
			$whereCond.=" and (employee_task_master.assign_by_employee_id='".$employeeID."')";
			if($search!='') {
				$whereCond.=" and (employee.name LIKE '%$search%')";
			}
			if($taskStatus!='') {
				$whereCond.=" and employee_task_master.status='".$taskStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS Pending, COUNT(CASE WHEN status = 'Inprogress' THEN 1 END) AS Inprogress, COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS Completed, COUNT(CASE WHEN status = 'Canceled' THEN 1 END) AS Canceled FROM employee_task_master where assign_by_employee_id='".$employeeID."'";
			
			$obj_model_task = $this->app->load_model("employee_task_master");
			$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$taskStatusList[]=["key"=>"Pending","value"=>"Pending (".$taskStatusResult[0]['Pending'].")"];
			$taskStatusList[]=["key"=>"Inprogress","value"=>"In Progress (".$taskStatusResult[0]['Inprogress'].")"];
			$taskStatusList[]=["key"=>"Completed","value"=>"Completed (".$taskStatusResult[0]['Completed'].")"];
			$taskStatusList[]=["key"=>"Canceled","value"=>"Canceled (".$taskStatusResult[0]['Canceled'].")"];
			//count end

			$obj_model_task = $this->app->load_model("employee_task_master");
			$obj_model_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_task_master.id desc");
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

			$obj_model_task = $this->app->load_model("employee_task_master");
			//$obj_model_task->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_task_master.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
				$employee_primary = $obj_model_employee->execute("SELECT",false,"","employee.id='".$item['employee_primary_id']."'");
				$employeeImage=$this->app->utility->get_image_url($employee_primary[0]["image"],'employee','large');

				$obj_model_client = $this->app->load_model("client");
				$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
				$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
				$client = $obj_model_client->execute("SELECT",false,"","client.id='".$item['client_id']."'","client.id desc limit 0,1");
				$clientImage=$this->app->utility->get_image_url($client[0]["image"],'client','large');
				
				$id=$this->app->utility->encrypt($item['id']);
				$allTaskList[]=array("id"=>$id,"number"=>'#'.$item['id'],"status"=>$item['status'],
				"purpose"=>$item['purpose'],
				"client_image"=>$clientImage,
				"client_company_name"=>$client[0]['company_name'],
				"client_address"=>$client[0]['client_detail_area'].' '.$client[0]['city_name'],
				"employee_image"=>$employeeImage,
				"employee_name"=>$employee_primary[0]['name'],
				"employee_designation"=>$employee_primary[0]['master_designation_name'],
				"date"=>date('d-m-Y', strtotime($item['task_datetime'])),
				"time"=>date('h:i A', strtotime($item['task_datetime'])),
				"assignBy"=>$item['employee_name'],
				"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
				"textStatusColor"=>"#d1e7dd",
				"textStatusBgColor"=>"#0f5031"
				);
			}
			$result=["allTaskList"=>$allTaskList,"taskStatus"=>$taskStatusList];
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