<?
class _logistic_assign_client_pickup_list extends controller {
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

		$pickupStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("pickupStatus"));
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			if($pickupStatus!='') {
				$whereCond.=" and employee_sample_pickup.status='".$pickupStatus."'";
			}

			if($search!='') {
				$whereCond.=" and (client.company_name LIKE '%$search%' or client.mobile LIKE '%$search%' or client.phone LIKE '%$search%' or client_detail.area LIKE '%$search%')";
			}

			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			$obj_model_client->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_client->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("client_id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup.employee_id='".$employeeID."' ".$whereCond."","employee_sample_pickup.id desc");
			$count=count($client);

			if($page==0) {
			//count start
			$query="SELECT COUNT(CASE WHEN employee_sample_pickup.status != '' THEN 1 END) AS AllCount, COUNT(CASE WHEN employee_sample_pickup.status = 'Pending' THEN 1 END) AS PendingCount, COUNT(CASE WHEN employee_sample_pickup.status = 'In Progress' THEN 1 END) AS InProgressCount, COUNT(CASE WHEN employee_sample_pickup.status = 'Completed' THEN 1 END) AS CompletedCount FROM employee_sample_pickup where employee_sample_pickup.employee_id='".$employeeID."'";
			
			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			$countQuery = $obj_model_client->execute("SELECT",false,$query);

			$clientStatusList[]=["key"=>"","value"=>"All (".$countQuery[0]['AllCount'].")"];
			$clientStatusList[]=["key"=>"Pending","value"=>"Pending (".$countQuery[0]['PendingCount'].")"];
			$clientStatusList[]=["key"=>"In Progress","value"=>"In Progress (".$countQuery[0]['InProgressCount'].")"];
			$clientStatusList[]=["key"=>"Completed","value"=>"Completed (".$countQuery[0]['CompletedCount'].")"];
			} else {
				$clientStatusList=array();
			}

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Client Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			$obj_model_client->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("client_id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("client_id"=>"client_id"));
			$obj_model_client->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$client = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup.employee_id='".$employeeID."' ".$whereCond."","employee_sample_pickup.id desc limit ".$start.",".$limit."");

			foreach($client as $item)
			{
				$address=$item['client_client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["client_image"],'client','large');
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
				$myClientList[]=array(
					"id"=>$id,
					"number"=>''.$item['id'],
					"clientID"=>$this->app->utility->encrypt($item['client_id']),
					"companyName"=>$item['client_company_name'],
					"image"=>$image,
					"address"=>$address,
					"statusName"=>$item['status'],
					"statusBgColor"=>'#0297CC',
					"statusColor"=>'#ffffff',
					"sampleCount"=>$item['sample_count'],
					"collectPayment"=>$item['payment_amount'],
					"startJourneyTime"=>'',
					"checkOutTime"=>'',
					"employeeName"=>$item['employee_name'],
					"employeeImage"=>$employeeImage,
					"employeePickUpTime"=>date('d-m-Y H:i a', strtotime($item['created_at']))
				);
			}
			$result=["myPickupList"=>$myClientList,"pickupStatusList"=>$clientStatusList];
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