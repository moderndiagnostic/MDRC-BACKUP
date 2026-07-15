<?
class _client_logistic_list extends controller {
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
		$clientStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("clientStatus"));

		$whereCond='client_logistic_assign.id!=""';
		if($employeeID!='' && $deviceType!='')
		{
			$whereCond.=" and (client_logistic_assign.logistic_manager_employee_id='".$employeeID."')";
			if($search!='') {
				$whereCond.=" and (employee.name LIKE '%$search%')";
			}
			if($clientStatus!='') {
				$whereCond.=" and client_logistic_assign.request_status='".$clientStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN request_status = 'Pending' THEN 1 END) AS Pending, COUNT(CASE WHEN request_status!= '' THEN 1 END) AS AllClient, COUNT(CASE WHEN request_status = 'Active' THEN 1 END) AS Active FROM client_logistic_assign";
			
			$obj_model_task = $this->app->load_model("client_logistic_assign");
			$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$taskStatusList[]=["key"=>"","value"=>"All (".$taskStatusResult[0]['AllClient'].")"];
			$taskStatusList[]=["key"=>"Pending","value"=>"Pending (".$taskStatusResult[0]['Pending'].")"];
			$taskStatusList[]=["key"=>"Active","value"=>"Approved (".$taskStatusResult[0]['Active'].")"];

			//count end

			$obj_model_task = $this->app->load_model("client_logistic_assign");
			$obj_model_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$obj_model_task->join_table("client", "left", array(), array("client_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"client_logistic_assign.id desc");
			$count=count($task);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["taskStatus"=>$taskStatusList];
				$message=array("message"=>"No Data Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("client_logistic_assign");
			$obj_model_task->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_task->join_table(["client"=>"client","city"=>"city"], "left", array(), array("city_id"=>"id"));
			$obj_model_task->join_table(["client"=>"client","client_detail"=>"client_detail"], "left", array(), array("id"=>"client_id"));
			$obj_model_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$obj_model_task->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"client_logistic_assign.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
				$clientImage=$this->app->utility->get_image_url($item["client_image"],'client','large');
				
				$allTaskList[]=array(
				"id"=>$this->app->utility->encrypt($item['id']),
				"clientId"=>$this->app->utility->encrypt($item['client_id']),
				"number"=>'#'.$item['id'],
				"status"=>$item['request_status'],
				"client_image"=>$clientImage,
				"client_company_name"=>$item['client_company_name'],
				"client_address"=>$item['client_detail_area'].' '.$item['city_name'],
				"employee_image"=>$employeeImage,
				"employee_name"=>$item['employee_name'],
				"employee_designation"=>$item['master_designation_name'],
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