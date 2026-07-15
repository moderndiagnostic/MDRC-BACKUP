<?
class _my_client_detail extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='' && $clientID!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$employeeID."'","employee.id desc limit 0,1");

			$whereCond.="and client.id='".$clientID."'";

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_bank", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_files", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$clientDetail = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc");
			$client=$clientDetail[0];
			
			if(count($client)<=0) {
				$message=array("message"=>"No Client Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$image=$this->app->utility->get_image_url($client["image"],'client','large');
			$address=$client['client_status']=='Client'?$client['client_detail_area'].' '.$client['city_name']:$client['client_address_google_city'];

			$myClientDetail=[
				"id"=>$this->app->utility->encrypt($client['id']),
				"image"=>$image,
				"name"=>$client['company_name'],
				"email"=>$client['email'],
				"mobile"=>$client['mobile'],
				"address"=>$address,
				"latitude"=>$client["client_address_google_latitude"],
				"longitude"=>$client["client_address_google_longitude"],
				"business_type"=>$client["client_detail_business_type"],
				"specialization"=>$client["client_detail_specialization"],
				"compnay_legal"=>$client["client_detail_compnay_legal"],
				"sample_pickup"=>$client["client_detail_sample_pickup"],
				"sample_pickup_frequency"=>$client["client_detail_sample_pickup_frequency"],
				"payment_mode"=>$client["client_detail_payment_mode"],
				"invoice_billing_cycle"=>$client["client_detail_invoice_billing_cycle"],
				"register_type"=>$client["client_detail_register_type"],
				"status"=>$client["client_status"],
				"files"=>$this->getclientFiles($client),
				"bankDetails"=>$this->getclientBank($client),
				"googleMap"=>$this->getclientGoogleMap($client),
			];

			$otherInfo=$this->getOtherInfoForDisplay($client);
			$personList=$this->getpersonList($client,$employee);
			$labList=$this->getlabList($client);
			$clientOtherDetails=$this->getclientOtherDetails($client);
			$buttons=$this->getButtons($client,$employeeID);
			$clientWaitSec=$this->getClientStatus($client);

			$result=[
				"myClientDetail"=>$myClientDetail,
				"personList"=>$personList,
				"otherInfo"=>$otherInfo,
				"labList"=>$labList,
				"clientOtherDetails"=>$clientOtherDetails,
				"clientWaitSec"=>$clientWaitSec,
				"buttons"=>$buttons
			];
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

	function getclientGoogleMap($client)
	{
		$address=[
			"google_address"=>$client["client_address_google_address"],
			"google_latitude"=>$client["client_address_google_latitude"],
			"google_longitude"=>$client["client_address_google_longitude"],
			"google_pincode"=>$client["client_address_google_pincode"],
			"google_city"=>$client["client_address_google_city"],
			"cityId"=>$this->app->utility->encrypt($client["city_id"]),
			"cityName"=>$client["city_name"]
		];
		return $address;
	}

	function getclientBank($client)
	{
		$bank=[
			"account_holder_name"=>$client["client_bank_account_holder_name"],
			"bank_name"=>$client["client_bank_bank_name"],
			"account_no"=>$client["client_bank_account_no"],
			"ifsc_code"=>$client["client_bank_ifsc_code"],
			"bank_address"=>$client["client_bank_bank_address"]
		];
		return $bank;
	}

	function getclientFiles($client)
	{
		$path=SERVER_ROOT.'/uploads/clientFile/'.$client['id'].'/';
		$files=[
			"company_photo"=>$client["client_files_company_photo"]!=''?$path.$client["client_files_company_photo"]:'',
			"aadhar_card_no"=>$client["client_files_aadhar_card_no"],
			"aadhar_card_front_photo"=>$client["client_files_aadhar_card_front_photo"]!=''?$path.$client["client_files_aadhar_card_front_photo"]:'',
			"aadhar_card_back_photo"=>$client["client_files_aadhar_card_back_photo"]!=''?$path.$client["client_files_aadhar_card_back_photo"]:'',
			"pancard_photo"=>$client["client_files_pancard_photo"]!=''?$path.$client["client_files_pancard_photo"]:'',
			"pancard_no"=>$client["client_files_pancard_no"],
			"incorporation_photo"=>$client["client_files_incorporation_photo"]!=''?$path.$client["client_files_incorporation_photo"]:'',
			"registration_photo"=>$client["client_files_registration_photo"]!=''?$path.$client["client_files_registration_photo"]:'',
			"gst_no"=>$client["client_files_gst_no"],
			"gst_photo"=>$client["client_files_gst_photo"]!=''?$path.$client["client_files_gst_photo"]:'',
			"sign_photo"=>$client["client_files_sign_photo"]!=''?$path.$client["client_files_sign_photo"]:'',
		];
		return $files;
	}

	function getClientStatus($client)
	{
		if($client['client_status']=='Request for Client') {
			return ["title"=>"Waiting For IT Approval","desc"=>"Send For Approval."];
		} else {
			return ["title"=>"","desc"=>""];
		}
		
	}

	function getButtons($client,$employeeID)
	{
		$editClient='No';
		$deleteClient='No';
		$confirmClient='No';
		if($client['client_detail_added_by_employee_id']==$employeeID && $client['client_status']=='Field Visit'){
			$editClient='Yes';
			$deleteClient='Yes';
			$confirmClient='Yes';
		}
		//$editClient='Yes';
		//$deleteClient='Yes';
			

		return ["editClient"=>$editClient,"deleteClient"=>$deleteClient,"confirmClient"=>$confirmClient];
	}

	function getOtherInfoForDisplay($client)
	{
		if($client["client_status"]=='Client') {
		$otherInfo[]=["label"=>"Status","value"=>$client['status']];
		$otherInfo[]=["label"=>"Credit Limit","value"=>$client['client_detail_credit_limit']];
		$otherInfo[]=["label"=>"Is Printing Lock","value"=>$client['client_detail_is_printing_lock']];
		$otherInfo[]=["label"=>"Billing Cycle","value"=>$client['client_detail_is_printing_lock']];
		$otherInfo[]=["label"=>"Payment Mode","value"=>$client['client_detail_is_printing_lock']];
		} else {
			$otherInfo=[];
		}
		return $otherInfo;
	}

	function getclientOtherDetails($client)
	{
		if($client["client_status"]=='Client') {
		$clientOtherDetails[]=["label"=>"Login ID","value"=>$client['mobile']];
		$clientOtherDetails[]=["label"=>"Login Password","value"=>$client['client_detail_ledger_report_password']];
		} else {
			$clientOtherDetails=[];
		}
		return $clientOtherDetails;
	}

	function getlabList($client)
	{
		$labList=[];
		if($client['client_detail_invoice_to_center']>0) {
			//get sales person details
			$obj_model_lab = $this->app->load_model("master_centre");
			$labDetail = $obj_model_lab->execute("SELECT",false,"","lms_center_id='".$client['client_detail_invoice_to_center']."'","master_centre.id desc limit 0,1");
			if(count($labDetail)>0){
				$lab=$labDetail[0];
				$image=$this->app->utility->get_image_url($lab["lab"],'lab','large');
				$labList[]=[
					"heading"=>'Processing Lab',
					"name"=>$lab['name'],
					"image"=>$image,
					"address"=>$lab['address'],
					"mobile"=>$lab['mobile']
				];
			}
		}
		return $labList;
	}

	function getpersonList($client,$loginEmployee)
	{
		$personList=[];
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
					"addButton"=>'No',
					"editButton"=>'No',
					"deleteButton"=>'No'
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
					"addButton"=>'No',
					"editButton"=>'No',
					"deleteButton"=>'No'
				];
			}
		}

		//Logistic Person Assigned
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$client['id']."'","id desc limit 0,1");
		
		if(count($logistic_assign)>0) {

			if($logistic_assign[0]['request_status']=='Active') {
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
						"addButton"=>'No',
						"editButton"=>'No',
						"deleteButton"=>'No',
						"acceptButton"=>'No'
					];
				}
			}

			if($logistic_assign[0]['request_status']=='Pending' || $logistic_assign[0]['request_status']=='Accept') {
				$message=$logistic_assign[0]['request_status']=='Pending'?"Waiting For Accept":"Waiting For Logistic Assign";
				//get sales person details
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$logistic_assign[0]['logistic_manager_employee_id']."'","employee.id desc limit 0,1");
				if(count($employeeDetail)>0){
					$employee=$employeeDetail[0];
					$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
					$personList[]=[
						"id"=>$this->app->utility->encrypt($employee['id']),
						"heading"=>'Logistic Manager Assign',
						"name"=>$employee['name'],
						"image"=>$image,
						"detail"=>$message,
						"mobile"=>$employee['mobile'],
						"addButton"=>'No',
						"editButton"=>'No',
						"deleteButton"=>'No',
						"acceptButton"=>'No'
					];
				}
			}

		} else {
			$addButton='No';
			if($loginEmployee[0]['master_designation_name']!='Logistics'){
				$addButton='Yes';
			}
			$personList[]=[
				"id"=>'',
				"heading"=>'Logistic Person Assign',
				"name"=>'',
				"image"=>'',
				"detail"=>'',
				"mobile"=>'',
				"addButton"=>$addButton,
				"editButton"=>'No',
				"deleteButton"=>'No',
				"acceptButton"=>'No'
			];
		}

		return $personList;
	}
}
?>