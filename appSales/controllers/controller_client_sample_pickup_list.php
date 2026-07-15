<?
class _client_sample_pickup_list extends controller {
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

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);

		$startDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("startDate"));
		$endDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("endDate"));

		$whereCond='';
		if($employeeID!='' && $deviceType!='' && $clientID!='')
		{
			
			$whereCond.=" and employee_sample_pickup.status='Completed' and client_id='".$clientID."'";

			if($startDate!='') {
				$whereCond.=" and STR_TO_DATE(`employee_sample_pickup`.`pickup_date`, '%d-%m-%Y') BETWEEN STR_TO_DATE('".$startDate."', '%d-%m-%Y') AND STR_TO_DATE('".$endDate."', '%d-%m-%Y')";
			}

			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			//$obj_model_client->join_table("client", "left", array(), array("client_id"=>"id"));
			$client = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup.id!='' ".$whereCond."","employee_sample_pickup.id desc");
			$count=count($client);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Data Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_client = $this->app->load_model("employee_sample_pickup");
			//$obj_model_client->join_table("client", "left", array(), array("client_id"=>"id"));
			//$obj_model_client->join_table("client_detail", "left", array("area"), array("client_id"=>"client_id"));
			//$obj_model_client->join_table("client_address", "left", array("google_address"), array("client_id"=>"client_id"));
			$obj_model_client->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$client = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup.id!='' ".$whereCond."","employee_sample_pickup.id desc limit ".$start.",".$limit."");

			foreach($client as $item)
			{
				$address=$item['client_client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
				$samplePickupList[]=array(
					"id"=>$id,
					"number"=>'#'.$item['id'],
					"sampleCount"=>$item['sample_count'],
					"collectPayment"=>$item['payment_amount'],
					"employeeName"=>$item['employee_name'],
					"employeeCode"=>$item['employee_lms_employee_code'],
					"employeeImage"=>$employeeImage,
					"employeeMobile"=>$item['employee_mobile'],
					"visited"=>date('d-m-Y h:i A', strtotime($item['created_at']))
				);
			}
			$result=["samplePickupList"=>$samplePickupList];
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