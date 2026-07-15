<?
class _hub_received_employee_pickup_list extends controller {
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

		$selectedEmployeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("selectedEmployee"));
		$selectedEmployeeID=$this->app->utility->decrypt($selectedEmployeeID);
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			if($pickupStatus!='') {
				$whereCond.=" and employee_sample_pickup.status='".$pickupStatus."'";
			}

			if($search!='') {
				$whereCond.=" and (employee_sample_pickup.id='$search')";
			}

			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			$obj_model_client->join_table("employee_sample_pickup_hub_data", "left", array(), array("id"=>"employee_sample_pickup_id"));
			$client = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup_hub_data.id!='' ".$whereCond."","employee_sample_pickup_hub_data.id desc");
			$count=count($client);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Client Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			$obj_model_client->join_table("employee_sample_pickup_hub_data", "left", array(), array("id"=>"employee_sample_pickup_id"));
			$obj_model_client->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("client_id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("client_id"=>"client_id"));
			//$obj_model_client->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_client->join_table(["employee_sample_pickup_hub_data"=>"employee_sample_pickup_hub_data","employee"=>"employee"], "left", array(), array("received_employee_id"=>"id"));
			$client = $obj_model_client->execute("SELECT",false,"","hub_received='Yes' and employee_sample_pickup_hub_data.id!='' ".$whereCond."","employee_sample_pickup_hub_data.id desc limit ".$start.",".$limit."");
			
			if(count($client)<=0) {
				$message=array("message"=>"No data Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			foreach($client as $item)
			{
				$address=$item['client_client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["client_image"],'client','large');
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
				$employeePickupList[]=array(
					"id"=>$id,
					"number"=>''.$item['id'],
					"clientID"=>$this->app->utility->encrypt($item['client_id']),
					"companyName"=>$item['client_company_name'],
					"image"=>$image,
					"address"=>$address,
					"statusName"=>$item['status'],
					"sampleCount"=>$item['sample_count'],
					"collectPayment"=>$item['payment_amount'],
					"employeeName"=>$item['employee_name'],
					"employeeImage"=>$employeeImage,
					"employeeDesc"=>'Approved By'
				);
			}
			$result=["employeePickupList"=>$employeePickupList];
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