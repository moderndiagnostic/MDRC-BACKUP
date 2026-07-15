<?
class _task_list_by_employee_by_client extends controller {
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

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("clientID"));
		$clientID=$this->app->utility->decrypt($clientID);

		$whereCond='employee_task_master.status!="Trash" and employee_task_master.status!="Draft"';
		if($employeeID!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employeeDetailArray = $obj_model_employee->execute("SELECT",false,"","status='Active' and id=".$employeeID);
			$image=$this->app->utility->get_image_url($employeeDetailArray[0]["image"],'employee','large');
			$employeeDetail=[
				"id"=>$this->app->utility->encrypt($employeeDetailArray[0]['id']),
				"image"=>$image,
				"name"=>$employeeDetailArray[0]['name'],
				"designation"=>$employeeDetailArray[0]['lms_employee_code'],
				"mobile"=>$employeeDetailArray[0]['mobile'],
			];

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("id"=>"client_id"));
			$clientDetailArray = $obj_model_client->execute("SELECT",false,"","client.id=".$clientID);

			$image=$this->app->utility->get_image_url($clientDetailArray[0]["image"],'client','large');
			$address=$clientDetailArray[0]['client_status']=='Client'?$clientDetailArray[0]['client_detail_area'].' '.$clientDetailArray[0]['city_name']:$clientDetailArray[0]['client_address_google_city'];
			$clientDetail=[
				"id"=>$this->app->utility->encrypt($clientDetailArray[0]['id']),
				"image"=>$image,
				"name"=>$clientDetailArray[0]['company_name'],
				"email"=>$clientDetailArray[0]['email'],
				"mobile"=>$clientDetailArray[0]['mobile'],
				"address"=>$address
			];

			$whereCond.=" and (employee_task_master.employee_primary_id='".$employeeID."' and employee_task_master.client_id='".$clientID."')";
			if($search!='') {
				$whereCond.=" and (employee_task_master.task_remark LIKE '%$search%')";
			}
			if($taskStatus!='') {
				$whereCond.=" and employee_task_master.status='".$taskStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN status != '' THEN 1 END) AS AllCount, COUNT(CASE WHEN status = 'Active' THEN 1 END) AS Active, COUNT(CASE WHEN status = 'Inprogress' THEN 1 END) AS Inprogress, COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS Completed, COUNT(CASE WHEN status = 'Canceled' THEN 1 END) AS Canceled FROM employee_task_master where employee_task_master.client_id='".$clientID."' and employee_primary_id='".$employeeID."'";
			
			$obj_model_task = $this->app->load_model("employee_task_master");
			$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$taskStatusList[]=["key"=>"","value"=>"All (".$taskStatusResult[0]['AllCount'].")"];
			$taskStatusList[]=["key"=>"Active","value"=>"Active (".$taskStatusResult[0]['Active'].")"];
			$taskStatusList[]=["key"=>"Inprogress","value"=>"In Progress (".$taskStatusResult[0]['Inprogress'].")"];
			$taskStatusList[]=["key"=>"Completed","value"=>"Completed (".$taskStatusResult[0]['Completed'].")"];
			
			//count end

			$obj_model_task = $this->app->load_model("employee_task_master");
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
			$obj_model_task->join_table("employee_task_master_detail", "left", array(), array("id"=>"employee_task_master_id"));
			$obj_model_task->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_task->join_table("client_address", "left", array(), array("client_id"=>"id"));
			$obj_model_task->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$obj_model_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"employee_task_master.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$address=$item['client_client_status']=='Client'?$item['client_detail_area']:$item['client_address_google_city'];
				$myTaskList[]=array("id"=>$id,"number"=>'#'.$item['id'],
				"status"=>$item['status'],
				"purpose"=>$item['purpose'],
				"client_meet"=>$item['employee_task_master_detail_meeting_client_meet'],
				"image"=>'',
				"company_name"=>$item['client_company_name'],
				"address"=>$address,
				"date"=>date('d-m-Y', strtotime($item['task_datetime'])),
				"time"=>date('h:i A', strtotime($item['task_datetime'])),
				"assignBy"=>$item['employee_name'],
				"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
				"textStatusColor"=>"#d1e7dd",
				"textStatusBgColor"=>"#0f5031"
				);
			}
			$result=["clientDetail"=>$clientDetail,"employeeDetail"=>$employeeDetail,"myTaskList"=>$myTaskList,"taskStatus"=>$taskStatusList,"totalVisit"=>(string)$count];
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