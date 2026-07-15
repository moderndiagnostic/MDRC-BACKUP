<?
class _logistic_assign_client_detail extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		$paymentMode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentMode"));

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);

		$samplePickupID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('samplePickupID'));
		$samplePickupID=$samplePickupID!=''?$this->app->utility->decrypt($samplePickupID):'';
		
		if($employeeID!='' && $clientID!='')
		{
			$condDate=date('d-m-Y');
			$samplePickupIDCond=$samplePickupID!=''?" and employee_sample_pickup.id='".$samplePickupID."'":'';
			$obj_model_sample_pickup = $this->app->load_model("employee_sample_pickup");
			if($samplePickupID!=''){
				$sample_pickup = $obj_model_sample_pickup->execute("SELECT",false,"","id!='' ".$samplePickupIDCond."","employee_sample_pickup.id desc");
			} else {
				$sample_pickup = $obj_model_sample_pickup->execute("SELECT",false,"","pickup_date='".$condDate."' and client_id='".$clientID."' and employee_id='".$employeeID."' and status!='Completed'","employee_sample_pickup.id desc");
			}
			

			$samplePickupID=count($sample_pickup)<=0?'':$sample_pickup[0]['id'];
			$samplePickupStart=count($sample_pickup)<=0?'':date('d-m-Y h:i A', strtotime($sample_pickup[0]['created_at']));

			$pickupDetail[]=["title"=>"Pickup Task ","value"=>$samplePickupID];
			$pickupDetail[]=["title"=>"Pickup Task Started","value"=>$samplePickupStart];
			
			$clientDetail=$this->getClientDetail($samplePickupID,$employeeID,$clientID);
			$pickupButtons=$this->getButtonStatus($samplePickupID,$employeeID,$clientID,$sample_pickup[0]['pickup_type']);
			$personAssigned=$this->getpersonAssigned($samplePickupID,$employeeID,$clientID);

			
			$result=["samplePickupID"=>$samplePickupID!=''?$this->app->utility->encrypt($samplePickupID):'',"pickupDetail"=>$pickupDetail,"clientDetail"=>$clientDetail,"pickupButtons"=>$pickupButtons['buttons'],"summary"=>$pickupButtons['summary'],"personList"=>$personAssigned];
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
			"address"=>$address,
			"latitude"=>empty($client["client_address_google_latitude"])?'':$client["client_address_google_latitude"],
			"longitude"=>empty($client["client_address_google_longitude"])?'':$client["client_address_google_longitude"],
			"clientTagName"=>$client["client_status"],
			"clientTagColor"=>'#5ccdde',
		];
		return $clientDetail;	
	}

	function getButtonStatus($samplePickupId,$employeeID,$clientID,$type='auto') {

		$obj_model_sampleUploadCount= $this->app->load_model("employee_sample_pickup_images");
		$sampleUploadCountResult = $obj_model_sampleUploadCount->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupId."'");
		$sampleUploadCount=count($sampleUploadCountResult);

		$buttonOption=false;
		$statusUpdate=[];
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$clientID."' and employee_id='".$employeeID."'");
		if($type=='auto'){
			$buttonOption=count($logistic_assign)>0?true:false;
		}else{
			$buttonOption=true;
		}

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
			  //$dis=$this->app->utility->getDistance($primaryLat,$primaryLong,$update['latitude'],$update['longitude'],'K');

			  	$apiKey = 'AIzaSyCMdIvv5Ajq0gtKqb7G3Yf-wHqsXZkq2rI';
				$directionsUrl = "https://maps.googleapis.com/maps/api/directions/json?origin=".$primaryLat.",".$primaryLong."&destination=".$update['latitude'].",".$update['longitude']."&key=".$apiKey;
        
				$response = file_get_contents($directionsUrl);
				$data = json_decode($response, true);
		
				// Extract distance in KM
				if (!empty($data['routes'][0]['legs'][0]['distance']['text'])) {
					$dis = $data['routes'][0]['legs'][0]['distance']['text'];
				}

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
				$image=$this->app->utility->get_image_url($update['checkin_photo'],'samplePickupUpdate/'.$update['employee_id'],'large');
				$checkIn=false;
				$summaryView=true;
				$statusUpdate[]=[
					"date"=>date('d-m-Y h:i A', strtotime($update['updated_at'])),
					"title"=>'Check In',
					"detail"=>'',
					"latitude"=>$update['latitude'],
					"longitude"=>$update['longitude'],
					"distance"=>$dis,
					"checkInPhoto"=>$image
				];
			}

			if($update['pickup_status']=='Check Out') {
				$buttonOption=false;
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
				$detail=$update['collect_payment_amount']>0 && $update['collect_payment_otp_mobile']!=''?$detail.' (OTP Verified with Other Mobile No : '.$update['collect_payment_otp_mobile'].') ':$detail;
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

		$buttons=[
			"buttonOption"=>$buttonOption,
			"startJourney"=>$startJourney,
			"endJourney"=>(!$startJourney && $endJourney && $checkIn && $checkOut) ? true:false,
			"checkIn"=>(!$startJourney && !$endJourney && $checkIn && $checkOut) ? true:false,
			"checkOut"=>(!$startJourney && !$endJourney && !$checkIn && $checkOut) ? true:false,
			"collectPayment"=>($collectPayment && !$startJourney && !$endJourney && !$checkIn && $checkOut) ? true:false,
			"collectSample"=>($collectSample && !$startJourney && !$endJourney && !$checkIn && $checkOut) ? true:false,
			"sampleUploadCount"=>$sampleUploadCount
		];

		return ["buttons"=>$buttons,"summary"=>$statusUpdate];
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