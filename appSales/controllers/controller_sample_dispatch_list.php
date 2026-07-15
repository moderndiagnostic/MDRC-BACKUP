<?
class _sample_dispatch_list extends controller {
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
		$dispatchStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("dispatchStatus"));

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));

		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			if($action=='Receive')
			{
				$obj_model_master_centre = $this->app->load_model("master_centre");
				$obj_model_master_centre->set_fields_to_get("name");
				$master_centre = $obj_model_master_centre->execute("SELECT",false,"","master_centre.employee_id='".$employeeID."'","");	
				$center_ids=implode(',', array_column($master_centre, 'id'));
			
				$whereCond.=" and (employee_sample_dispatch.sent_center_id IN (".$center_ids."))";
			}
			else
			{
				$whereCond.=" and (employee_sample_dispatch.employee_id='".$employeeID."')";
			}
			$countCond=$whereCond;
			if($search!='') {
				$whereCond.=" and (employee_sample_dispatch.courier_type LIKE '%$search%')";
			}
			if($dispatchStatus!='') {
				$whereCond.=" and employee_sample_dispatch.status='".$dispatchStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN status = 'Dispatched' THEN 1 END) AS Dispatched, COUNT(CASE WHEN status= 'Delivered' THEN 1 END) AS Delivered, COUNT(CASE WHEN status != '' THEN 1 END) AS AllD FROM employee_sample_dispatch WHERE status!='Trash'".$countCond;
			
			$obj_model_task = $this->app->load_model("employee_sample_dispatch");
			$taskStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$dispatchStatusList[]=["key"=>"","value"=>"All (".$taskStatusResult[0]['AllD'].")"];
			$dispatchStatusList[]=["key"=>"Dispatched","value"=>"Dispatched (".$taskStatusResult[0]['Dispatched'].")"];
			$dispatchStatusList[]=["key"=>"Delivered","value"=>"Delivered (".$taskStatusResult[0]['Delivered'].")"];

			//count end

			$obj_model_task = $this->app->load_model("employee_sample_dispatch");
			//$obj_model_task->join_table("employee_sample_dispatch_other_detail", "left", array(), array("id"=>"employee_sample_dispatch_id"));
			$task = $obj_model_task->execute("SELECT",false,"","employee_sample_dispatch.status!='Trash'".$whereCond,"employee_sample_dispatch.id desc");
			$count=count($task);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["dispatchStatus"=>$dispatchStatusList];
				$message=array("message"=>"No Data Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("employee_sample_dispatch");
			$obj_model_task->join_table("employee_sample_dispatch_other_detail", "left", array(), array("id"=>"employee_sample_dispatch_id"));
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_task->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
			$obj_model_task->join_table(["employee"=>"employeeR"], "left", array(), array("receive_employee_id"=>"id"));
			$obj_model_task->join_table("master_centre", "left", array(), array("sent_center_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"","employee_sample_dispatch.status!='Trash'".$whereCond,"employee_sample_dispatch.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$allSampleList[]=array("id"=>$this->app->utility->encrypt($item['id']),
				"number"=>'#'.$item['id'],
				"status"=>$item['status'],
				"courierType"=>$item['courier_type'],
				"courierName"=>$item['courier_name'],
				"deliveryDate"=>$item['courier_delivery_date'],
				"deliveryTime"=>$item['employee_sample_dispatch_other_detail_courier_delivery_time'],
				"centerName"=>$item['master_centre_name'],
				"centerAddress"=>$item['master_centre_address'],
				"pickup_employee_name"=>$item['receive_employee_id']>0?$item['employeeR_name']:'', //Receive By
				"pickup_employee_designation"=>'',
				"accept_employee_name"=>'',
				"accept_employee_designation"=>'',
				"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
				"textStatusColor"=>"#d1e7dd",
				"textStatusBgColor"=>"#0f5031"
				);
			}
			$result=["allSampleList"=>$allSampleList,"dispatchStatus"=>$dispatchStatusList];
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