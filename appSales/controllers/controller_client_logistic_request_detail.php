<?
class _client_logistic_request_detail extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$clientId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientId'));
		$clientId=$this->app->utility->decrypt($clientId);

		if($employeeID!='' && $clientId!='')
		{
			$obj_model = $this->app->load_model("client_logistic_assign");
			$request = $obj_model->execute("SELECT",false,"","client_id='".$clientId."'");
			$createdAt=date('d-m-Y h:i A', strtotime($request[0]['created_at']));

			$pickupDetail[]=["title"=>"Request ID","value"=>$request[0]['id']];
			$pickupDetail[]=["title"=>"Request On","value"=>$createdAt];
			
			$clientID=$request[0]['client_id'];
			$getClientDetailResult=$this->getClientDetail($request[0]['id'],$employeeID,$clientID,$pickupDetail);
			$clientDetail=$getClientDetailResult['clientDetail'];
			$pickupDetails=$getClientDetailResult['pickupDetail'];
			$pickupButtons=$this->getButtonStatus($request,$employeeID,$clientID);
			$personAssigned=$this->getpersonAssigned($request,$employeeID,$clientID);

			$result=["pickupDetail"=>$pickupDetails,"clientDetail"=>$clientDetail,"summary"=>$pickupButtons['summary'],"personList"=>$personAssigned];
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

	function getClientDetail($requestID,$employeeID,$clientID,$pickupDetail)
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
			"latitude"=>$client["client_address_google_latitude"],
			"longitude"=>$client["client_address_google_longitude"],
			"clientTagName"=>$client["client_status"],
			"clientTagColor"=>'#5ccdde',
		];

		$pickupDetail[]=["title"=>"Pickup On","value"=>$client['client_detail_sample_pickup']];
		$pickupDetail[]=["title"=>"Frequency","value"=>$client['client_detail_sample_pickup_frequency']];

		return ["clientDetail"=>$clientDetail,"pickupDetail"=>$pickupDetail];
	}

	function getButtonStatus($request,$employeeID,$clientID) {

		$obj_model = $this->app->load_model("client_logistic_assign_history");
		$result = $obj_model->execute("SELECT",false,"","client_logistic_assign_id='".$request[0]['id']."'","");
		
		foreach($result as $update){
			$statusUpdate[]=[
				"date"=>date('d-m-Y h:i A', strtotime($update['created_at'])),
				"title"=>$update['title'],
				"detail"=>'',
				"latitude"=>'',
				"longitude"=>'',
				"distance"=>''
			];
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
					"mobile"=>$employee['mobile'],
					"edit"=>'No',
					"delete"=>'No',
					"accept"=>'No',
					"reject"=>'No',
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
					"mobile"=>$employee['mobile'],
					"edit"=>'No',
					"delete"=>'No',
					"accept"=>'No',
					"reject"=>'No',
				];
			}
		}

		//Logistic Person Assigned
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$clientID."'","id desc limit 0,1");
		
		if(count($logistic_assign)>0) {

			if($logistic_assign[0]['request_status']=='Active')
			{
				//get sales person details
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$logistic_assign[0]['employee_id']."'","employee.id desc limit 0,1");
				if(count($employeeDetail)>0) {
					$employee=$employeeDetail[0];
					$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
					$personList[]=[
						"id"=>$this->app->utility->encrypt($employee['id']),
						"heading"=>'Logistic Person Assign',
						"name"=>$employee['name'],
						"image"=>$image,
						"detail"=>$employee['master_designation_name'],
						"mobile"=>$employee['mobile'],
						"edit"=>'Yes',
						"delete"=>'Yes',
						"accept"=>'No',
						"reject"=>'No',
					];
				}
			}

			if($logistic_assign[0]['logistic_manager_employee_id']!='')
			{
				$accept='No';
				$reject='No';
				if($logistic_assign[0]['request_status']=='Pending' || $logistic_assign[0]['request_status']=='Accept')
				{
					$accept='Yes';
					$reject='Yes';
				}

				//get sales person details
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$logistic_assign[0]['logistic_manager_employee_id']."'","employee.id desc limit 0,1");
				if(count($employeeDetail)>0) {
					$employee=$employeeDetail[0];
					$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
					$personList[]=[
						"id"=>$this->app->utility->encrypt($employee['id']),
						"heading"=>'Logistic Manager',
						"name"=>$employee['name'],
						"image"=>$image,
						"detail"=>$employee['master_designation_name'],
						"mobile"=>$employee['mobile'],
						"edit"=>'No',
						"delete"=>'No',
						"accept"=>$accept,
						"reject"=>$reject,
					];
				}
			}

		} 
		return $personList;
	}
}
?>