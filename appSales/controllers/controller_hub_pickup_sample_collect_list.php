<?
class _hub_pickup_sample_collect_list extends controller {
	
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$samplePickupID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('samplePickupID'));
		$samplePickupID=$this->app->utility->decrypt($samplePickupID);

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('action'));

		$employeeLabId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeLabId'));

		if($employeeID=='' || $samplePickupID=="")
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt);
			exit;
		}
		
		if($action=='Sample Collect List') {
			
			$obj_model_client = $this->app->load_model("employee_sample_pickup_images");
			$obj_model_client->join_table("employee_sample_pickup", "left", array(), array("employee_sample_pickup_id"=>"id"));
			$sample = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup_images.employee_sample_pickup_id='".$samplePickupID."'","employee_sample_pickup_images.id ASC");
			
			$pickupData[]=["title"=>'#'.$samplePickupID,"value"=>$sample[0]['employee_sample_pickup_status']];
			$pickupData[]=["title"=>'Date',"value"=>date('d-m-Y', strtotime($sample[0]['employee_sample_pickup_created_at']))];
			$pickupData[]=["title"=>'Total Sample',"value"=>count($sample)." Collected"];

			foreach($sample as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'samplePickup/'.$item['employee_sample_pickup_id'],'large');
				$pickupSampleList[]=array(
					"id"=>$id,
					"d_id"=>$item['id'],
					"parent_id"=>$item['parent_id']>0?$item['parent_id']:'',
					"number"=>''.$item['id'],
					"detail"=>''.$item['id'],
					"image"=>$image,
					"barcode"=>$item['barcode'],
					"date"=>date('d-m-Y h:i A', strtotime($item['updated_at'])),
					"remark"=>$item['remark']
				);
			}

			$employeeLabSelection=[];
			$obj_master_centre = $this->app->load_model("master_centre");
			$master_centre = $obj_master_centre->execute("SELECT",false,"","employee_id='".$employeeID."'","master_centre.name ASC");
			foreach($master_centre as $item){
				$employeeLabSelection[]=["id"=>$item['id'],"name"=>$item['name']];
			}

			$clientID=$sample[0]['employee_sample_pickup_client_id'];
			$clientDetail=$this->getClientDetail($samplePickupID,$employeeID,$clientID);
			$pickupButtons=$this->getButtonStatus($samplePickupID,$employeeID,$clientID);
			$personAssigned=$this->getpersonAssigned($samplePickupID,$employeeID,$clientID);

			$result=[
				"pickupSampleList"=>$pickupSampleList,
				"pickupData"=>$pickupData,
				"acceptButtonShow"=>'Yes',
				"employeeLabSelection"=>$employeeLabSelection,
				"clientDetail"=>$clientDetail,
				"summary"=>$pickupButtons['summary'],
				"personList"=>$personAssigned
			];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		elseif($action=='Accept Sample') {

			$data_t=array();
			$data_t['hub_received']='Yes';
			$obj_model_table=$this->app->load_model("employee_sample_pickup");
			$obj_model_table->map_fields($data_t);
			$obj_model_table->execute("UPDATE",false,"","id='".$samplePickupID."'");

			$data_t=array();
			$data_t['employee_sample_pickup_id']=$samplePickupID;
			$data_t['received_employee_id']=$employeeID;
			$data_t['master_centre_id']=$employeeLabId;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_table=$this->app->load_model("employee_sample_pickup_hub_data");
			$obj_model_table->map_fields($data_t);
			$obj_model_table->execute("INSERT",false,"","");

			$message=array("message"=>"success","msgCode"=>"1");
		}
		else
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}

	function getClientDetail($samplePickupId,$employeeID,$clientID)
	{
		$obj_model_client = $this->app->load_model("client");
		$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
		$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
		$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
		$clientDetail = $obj_model_client->execute("SELECT",false,"","client.id='".$clientID."'","client.id desc");
		$client=$clientDetail[0];
		
		if(count($client)<=0) {
			$message=array("message"=>"No Client Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$image=$this->app->utility->get_image_url($client["image"],'client','large');
		$address=$client['client_status']=='Client'?$client['client_detail_area'].' '.$client['city_name']:$client['client_address_google_city'];
		$clientDetail=[
			"id"=>$this->app->utility->encrypt($client['id']),
			"image"=>$image,
			"name"=>$client['company_name'],
			"email"=>$client['email'],
			"mobile"=>$client['mobile'],
			"address"=>$address??'',
			"latitude"=>$client["client_address_google_latitude"]??'',
			"longitude"=>$client["client_address_google_longitude"]??'',
			"clientTagName"=>$client["client_status"],
			"clientTagColor"=>'#5ccdde',
		];
		return $clientDetail;	
	}

	function getButtonStatus($samplePickupId,$employeeID,$clientID) {

		$obj_model_sampleUploadCount= $this->app->load_model("employee_sample_pickup_images");
		$sampleUploadCountResult = $obj_model_sampleUploadCount->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupId."'");
		$sampleUploadCount=count($sampleUploadCountResult);

		$buttonOption=false;
		$statusUpdate=[];
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$clientID."' and employee_id='".$employeeID."'");
		$buttonOption=count($logistic_assign)>0?true:false;

		$summaryView=false;
		$startJourney=true;
		$endJourney=true;
		$checkIn=true;
		$checkOut=true;
		$collectPayment=true;
		$collectSample=true;
		

		$obj_model_sample_pickup_update = $this->app->load_model("employee_sample_pickup_update");
		$sample_pickup_update = $obj_model_sample_pickup_update->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupId."'","");
		
		
		foreach($sample_pickup_update as $update){

			$dis='';
			if($update['pickup_status']=='Start Journey')
			{
			  $primaryLat=$update['latitude'];
			  $primaryLong=$update['longitude'];
			  $dis='0 KM';
			}
			if($update['pickup_status']=='End Journey')
			{
			  $dis=$this->app->utility->getDistance($primaryLat,$primaryLong,$update['latitude'],$update['longitude'],'K');
			}
			if($update['pickup_status']=='Check In')
			{
			  $dis=$this->app->utility->getDistance($primaryLat,$primaryLong,$update['latitude'],$update['longitude'],'K');
			}
			if($update['pickup_status']=='Check Out')
			{
			  $dis=$this->app->utility->getDistance($primaryLat,$primaryLong,$update['latitude'],$update['longitude'],'K');
			}

			if($update['pickup_status']=='Start Journey') {
				$startJourney=false;
				$summaryView=true;
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'Start Journey',
					"detail"=>'',
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>$dis
				];
			}

			if($update['pickup_status']=='End Journey') {
				$endJourney=false;
				$summaryView=true;
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'End Journey',
					"detail"=>'',
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>$dis
				];
			}

			if($update['pickup_status']=='Check In') {
				$checkIn=false;
				$summaryView=true;
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'Check In',
					"detail"=>'',
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>$dis
				];
			}

			if($update['pickup_status']=='Check Out') {
				$checkOut=false;
				$summaryView=true;
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'Check Out',
					"detail"=>'',
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>$dis
				];
			}

			if($update['pickup_status']=='Sample Collect') {
				if($sampleUploadCount==0){ $collectSample=false; }
				$summaryView=true;
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'Sample Collect',
					"detail"=>$sampleUploadCount,
					"latitude"=>'',
					"longitude"=>'',
					"distance"=>$dis
				];
			}
			
			if($update['pickup_status']=='Payment' && $update['collect_payment_otp_verify']=='Yes') {
				$collectPayment=false;
				$summaryView=true;
				$detail=$update['collect_payment_amount']>0?"Payment Collected Rs.".$update['collect_payment_amount']:"No Payment Collection";
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'Payment',
					"detail"=>$detail,
					"latitude"=>'',
					"longitude"=>'',
					"distance"=>$dis
				];
			}

		}
		return ["summary"=>$statusUpdate];
	}

	function getpersonAssigned($samplePickupId,$employeeID,$clientID) {
		
		$personList=[];
		$obj_model_client = $this->app->load_model("client");
		$obj_model_client->join_table("client_detail", "left", array("added_by_employee_id"), array("id"=>"client_id"));
		$clients = $obj_model_client->execute("SELECT",false,"","client.id='".$clientID."'","client.id desc limit 0,1");
		$client=$clients[0];

		if($client['lms_employee_id']>0) {
			//get sales person details
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employeeDetail = $obj_model_employee->execute("SELECT",false,"","lms_employee_id='".$client['lms_employee_id']."'","employee.id desc limit 0,1");
			if(count($employeeDetail)>0){
				$employee=$employeeDetail[0];
				$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
				$personList[]=[
					"id"=>'',
					"heading"=>'Sales Person Tagged',
					"name"=>$employee['name'],
					"image"=>$image,
					"detail"=>$employee['master_designation_name'],
					"mobile"=>$employee['mobile']
				];
			}
		}

		if($client['client_detail_added_by_employee_id']>0 && $client["client_status"]!='Client') {
			//get sales person details
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$client['client_detail_added_by_employee_id']."'","employee.id desc limit 0,1");
			if(count($employeeDetail)>0){
				$employee=$employeeDetail[0];
				$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
				$personList[]=[
					"id"=>'',
					"heading"=>'Sales Person Tagged',
					"name"=>$employee['name'],
					"image"=>$image,
					"detail"=>$employee['master_designation_name'],
					"mobile"=>$employee['mobile']
				];
			}
		}

		//Logistic Person Assigned
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$clientID."'","id desc limit 0,1");
		
		if(count($logistic_assign)>0) {
			//get sales person details
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$logistic_assign[0]['employee_id']."'","employee.id desc limit 0,1");
			if(count($employeeDetail)>0){
				$employee=$employeeDetail[0];
				$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
				$personList[]=[
					"id"=>$this->app->utility->encrypt($employee['id']),
					"heading"=>'Logistic Person Assign',
					"name"=>$employee['name'],
					"image"=>$image,
					"detail"=>$employee['master_designation_name'],
					"mobile"=>$employee['mobile'],
				];
			}
		} 
		return $personList;
	}

}
?>