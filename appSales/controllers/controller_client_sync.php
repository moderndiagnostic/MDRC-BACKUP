<?
class _client_sync extends controller{
	function init(){
	}
	function onload()
	{
		/*
		//add master
		//---------
		$obj_model_temp_center=$this->app->load_model("temp_center");
		$center=$obj_model_temp_center->execute("SELECT",false,"","","id desc");
		foreach($center as $item)
		{
			$obj_model_state=$this->app->load_model("state");
			$state=$obj_model_state->execute("SELECT",false,"","name='".$item['State']."'");
			if(count($state)<=0) {
				$data=array();
				$data['name']=$item['State'];
				$data['status']='Active';
				$data['created_at']=date("Y-m-d H:i:s");
				$obj_model_state=$this->app->load_model("state");
				$obj_model_state->map_fields($data);
				$stateID=$obj_model_state->execute("INSERT");
			} else {
				$stateID=$state[0]['id'];
			}

			$obj_model_city=$this->app->load_model("city");
			$cityR=$obj_model_city->execute("SELECT",false,"","name='".$item['City']."'");
			if(count($cityR)<=0) {
				$data=array();
				$data['state_id']=$stateID;
				$data['name']=$item['City'];
				$data['slug']=$this->app->utility->seo_url($item['City']);
				$data['status']='Active';
				$data['created_at']=date("Y-m-d H:i:s");
				$obj_model_city=$this->app->load_model("city");
				$obj_model_city->map_fields($data);
				$cityID=$obj_model_city->execute("INSERT");
			} else {
				$cityID=$cityR[0]['id'];
			}

			$obj_model_master_businesszone=$this->app->load_model("master_businesszone");
			$businesszone=$obj_model_master_businesszone->execute("SELECT",false,"","name='".$item['BusinessZone']."'");
			if(count($businesszone)<=0) {
				$data=array();
				$data['name']=$item['BusinessZone'];
				$data['status']='Active';
				$data['created_at']=date("Y-m-d H:i:s");
				$obj_model_master_businesszone=$this->app->load_model("master_businesszone");
				$obj_model_master_businesszone->map_fields($data);
				$businesszoneID=$obj_model_master_businesszone->execute("INSERT");
			} else {
				$businesszoneID=$businesszone[0]['id'];
			}

			$obj_model_master_centre=$this->app->load_model("master_centre");
			$master_centre=$obj_model_master_centre->execute("SELECT",false,"","lms_center_id='".$item['SAPCentreCode']."'");
			if(count($master_centre)>0) {
				//update
		
			} else {
				//insert
				$data_t=array();
				$data_t['lms_center_id']=$item['SAPCentreCode'];
				$data_t['name']=$item['CentreName'];
				$data_t['center_type']=$item['CentreType'];
				$data_t['address']=$item['Address'];
				$data_t['cityzone']=$item['CityZone'];
				$data_t['area']=$item['Locality'];
				$data_t['mobile']=$item['Mobile'];
				$data_t['contact_person']=$item['ContactPerson'];
				$data_t['contact_mobile']=$item['ContactPersonMobile'];
				$data_t['payment_mode']=$item['PaymentMode'];
				$data_t['status']=$item['IsActive']=='Yes'?'Active':'Inactive';
				$data_t['city_id']=$cityID;
				$data_t['state_id']=$stateID;
				$data_t['master_businesszone_id']=$businesszoneID;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$obj_model_employee=$this->app->load_model("master_centre");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("INSERT");
			}
		}
		exit; */




		/*
		//employee sync
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://182.156.200.228/mdrcnew/api/BookingAPI/GetEmployee',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>'{"EmployeeID":""}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
		));
		$response = curl_exec($curl);
		//print_r($response); exit;
		curl_close($curl);

		$res=json_decode($response,true);
		
		$positions=['Director','Vice President - Sales','General Manager','Deputy General Manager','Zonal Sales Manager','Regional Sales Manager','Area Sales Manager','Business  Development Executive','Logistics Manager','Logistics'];
		foreach($res['data'] as $item)
		{
			if(in_array(strtolower($item['Designation']), array_map('strtolower',$positions))) {
				//city add in master
				$obj_model_city=$this->app->load_model("city");
				$cityR=$obj_model_city->execute("SELECT",false,"","name='".$item['city']."'");
				if(count($cityR)<=0) {
					$data=array();
					$data['name']=$item['city'];
					$data['slug']=$this->app->utility->seo_url($item['city']);
					$data['status']='Active';
					$data['created_at']=date("Y-m-d H:i:s");
					$obj_model_city=$this->app->load_model("city");
					$obj_model_city->map_fields($data);
					$cityID=$obj_model_city->execute("INSERT");
				} else {
					$cityID=$cityR[0]['id'];
				}

				$obj_model_city=$this->app->load_model("master_designation");
				$master_designation=$obj_model_city->execute("SELECT",false,"","name='".$item['Designation']."'");
				if(count($master_designation)<=0) {
					$data=array();
					$data['name']=$item['Designation'];
					$data['status']='Active';
					$data['created_at']=date("Y-m-d H:i:s");
					$data['updated_at']=date("Y-m-d H:i:s");
					$obj_model_master_designation=$this->app->load_model("master_designation");
					$obj_model_master_designation->map_fields($data);
					$masterDesignationID=$obj_model_master_designation->execute("INSERT");
				} else {
					$masterDesignationID=$master_designation[0]['id'];
				}

				//check if employer exist or not
				$obj_model_employee=$this->app->load_model("employee");
				$employee=$obj_model_employee->execute("SELECT",false,"","lms_employee_id='".$item['Employee_id']."'");
				
				if(count($employee)>0) {
					//update
					$data_t=array();
					$data_t['lms_employee_id']=$item['Employee_id'];
					$data_t['lms_employee_code']=$item['Emoloyee_Code'];
					$data_t['name']=$item['EmoloyeeName'];
					$data_t['email']=$item['email'];
					$data_t['mobile']=$item['mobile'];
					$data_t['master_designation_id']=$masterDesignationID;
					$data_t['city_id']=$cityID;
					$data_t['login_password']='Admin';
					$data_t['status']=$item['isactive']==1?'Active':'Inactive';
					$data_t['reporting_employee_lms_id']=$item['Reporting_Employee_ID'];
					$data_t['created_at']=date("Y-m-d H:i:s");
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee=$this->app->load_model("employee");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$employee[0]['id']."'");

					$data_t=array();
					$data_t['employee_id']=$employee[0]['id'];
					$data_t['area']=$item['Locality'];
					$data_t['master_centre_lms_ids']=$item['Tagcentreid'];
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee_detail=$this->app->load_model("employee_detail");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("UPDATE",false,"","employee_id='".$employee[0]['id']."'");

					//delete old
					$obj_model_employee_centre=$this->app->load_model("employee_centre");
					$obj_model_employee_centre->execute("DELETE",false,"","employee_id='".$employee[0]['id']."'");

					$Tagcentreid=explode(',',$item['Tagcentreid']);
					foreach($Tagcentreid as $key=>$value)
					{
						$data_t=[];
						$data_t['employee_id']=$employee[0]['id'];
						$data_t['lms_centre_id']=$value;
						$obj_model_employee_centre=$this->app->load_model("employee_centre");
						$obj_model_employee_centre->map_fields($data_t);
						$obj_model_employee_centre->execute("INSERT");
					}

				} else {
					//insert
					$data_t=array();
					$data_t['lms_employee_id']=$item['Employee_id'];
					$data_t['lms_employee_code']=$item['Emoloyee_Code'];
					$data_t['name']=$item['EmoloyeeName'];
					$data_t['email']=$item['email'];
					$data_t['mobile']=$item['mobile'];
					$data_t['master_designation_id']=$masterDesignationID;
					$data_t['city_id']=$cityID;
					$data_t['login_password']='Admin';
					$data_t['status']=$item['isactive']==1?'Active':'Inactive';
					$data_t['reporting_employee_lms_id']=$item['Reporting_Employee_ID'];
					$data_t['created_at']=date("Y-m-d H:i:s");
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee=$this->app->load_model("employee");
					$obj_model_employee->map_fields($data_t);
					$employeeId=$obj_model_employee->execute("INSERT");

					$data_t=array();
					$data_t['employee_id']=$employeeId;
					$data_t['area']=$item['Locality'];
					$data_t['master_centre_lms_ids']=$item['Tagcentreid'];
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee_detail=$this->app->load_model("employee_detail");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("INSERT");

					$Tagcentreid=explode(',',$item['Tagcentreid']);
					foreach($Tagcentreid as $key=>$value)
					{
						$data_t=[];
						$data_t['employee_id']=$employeeId;
						$data_t['lms_centre_id']=$value;
						$obj_model_employee_centre=$this->app->load_model("employee_centre");
						$obj_model_employee_centre->map_fields($data_t);
						$obj_model_employee_centre->execute("INSERT");
					}
				}
			}	
		}
		exit; 
		//employee end
		*/
		
		 /*
		//client sync
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://182.156.200.228/mdrcnew/api/BookingAPI/GetPanel',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>'{"PanelID":""}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$res=json_decode($response,true);
		
		foreach($res['data'] as $item)
		{
				$obj_model_state=$this->app->load_model("state");
				$state=$obj_model_state->execute("SELECT",false,"","name='".$item['State']."'");
				if(count($state)<=0) {
					$data=array();
					$data['name']=$item['State'];
					$data['status']='Active';
					$data['created_at']=date("Y-m-d H:i:s");
					$obj_model_state=$this->app->load_model("state");
					$obj_model_state->map_fields($data);
					$stateID=$obj_model_state->execute("INSERT");
				} else {
					$stateID=$state[0]['id'];
				}

				$obj_model_city=$this->app->load_model("city");
				$cityR=$obj_model_city->execute("SELECT",false,"","name='".$item['City']."'");
				if(count($cityR)<=0) {
					$data=array();
					$data['state_id']=$stateID;
					$data['name']=$item['City'];
					$data['slug']=$this->app->utility->seo_url($item['City']);
					$data['status']='Active';
					$data['created_at']=date("Y-m-d H:i:s");
					$obj_model_city=$this->app->load_model("city");
					$obj_model_city->map_fields($data);
					$cityID=$obj_model_city->execute("INSERT");
				} else {
					$cityID=$cityR[0]['id'];
				}

				$obj_model_master_businesszone=$this->app->load_model("master_businesszone");
				$businesszone=$obj_model_master_businesszone->execute("SELECT",false,"","name='".$item['BusinessZone']."'");
				if(count($businesszone)<=0) {
					$data=array();
					$data['name']=$item['BusinessZone'];
					$data['status']='Active';
					$data['created_at']=date("Y-m-d H:i:s");
					$obj_model_master_businesszone=$this->app->load_model("master_businesszone");
					$obj_model_master_businesszone->map_fields($data);
					$businesszoneID=$obj_model_master_businesszone->execute("INSERT");
				} else {
					$businesszoneID=$businesszone[0]['id'];
				}

				//check if client exist or not
				$obj_model_client=$this->app->load_model("client");
				$client=$obj_model_client->execute("SELECT",false,"","panel_id='".$item['Panel_ID']."'");
				
				if(count($client)>0) {
					//update
					$data_t=array();
					$data_t['panel_id']=$item['Panel_ID'];
					$data_t['company_name']=$item['Company_Name'];
					$data_t['phone']=$item['Phone'];
					$data_t['mobile']=$item['Mobile'];
					$data_t['city_id']=$cityID;
					$data_t['state_id']=$stateID;
					$data_t['master_businesszone_id']=$businesszoneID;
					$data_t['status']=$item['isactive']==1?'Active':'Inactive';
					$data_t['lms_employee_id']=$item['SalesManagerID'];
					$data_t['created_at']=date("Y-m-d H:i:s");
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee=$this->app->load_model("client");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$client[0]['id']."'");

					$data_t=array();
					$data_t['client_id']=$client[0]['id'];
					$data_t['address']=$item['Add1'];
					$data_t['area']=$item['AREA'];
					$data_t['ledger_report_password']=$item['LedgerReportPassword'];
					$data_t['invoice_to_center']=$item['InvoiceTo'];
					$data_t['booking_lock_reason']=$item['BookingLockReason'];
					$data_t['credit_limit']=$item['CreditLimit'];
					$data_t['is_printing_lock']=$item['IsPrintingLock'];
					$obj_model_employee_detail=$this->app->load_model("client_detail");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("UPDATE",false,"","client_id='".$client[0]['id']."'");

				} else {
					//insert
					$data_t=array();
					$data_t['panel_id']=$item['Panel_ID'];
					$data_t['company_name']=$item['Company_Name'];
					$data_t['phone']=$item['Phone'];
					$data_t['mobile']=$item['Mobile'];
					$data_t['city_id']=$cityID;
					$data_t['state_id']=$stateID;
					$data_t['master_businesszone_id']=$businesszoneID;
					$data_t['status']=$item['isactive']==1?'Active':'Inactive';
					$data_t['lms_employee_id']=$item['SalesManagerID'];
					$data_t['created_at']=date("Y-m-d H:i:s");
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee=$this->app->load_model("client");
					$obj_model_employee->map_fields($data_t);
					$clientId=$obj_model_employee->execute("INSERT");

					$data_t=array();
					$data_t['client_id']=$clientId;
					$data_t['address']=$item['Add1'];
					$data_t['area']=$item['AREA'];
					$data_t['ledger_report_password']=$item['LedgerReportPassword'];
					$data_t['invoice_to_center']=$item['InvoiceTo'];
					$data_t['booking_lock_reason']=$item['BookingLockReason'];
					$data_t['credit_limit']=$item['CreditLimit'];
					$data_t['is_printing_lock']=$item['IsPrintingLock'];
					$obj_model_employee_detail=$this->app->load_model("client_detail");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("INSERT");

					$data_t=array();
					$data_t['client_id']=$clientId;
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee_detail=$this->app->load_model("client_address");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("INSERT");

					$data_t=array();
					$data_t['client_id']=$clientId;
					$data_t['created_at']=date("Y-m-d H:i:s");
					$obj_model_employee_detail=$this->app->load_model("client_bank");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("INSERT");

					$data_t=array();
					$data_t['client_id']=$clientId;
					$data_t['created_at']=date("Y-m-d H:i:s");
					$data_t['updated_at']=date("Y-m-d H:i:s");
					$obj_model_employee_detail=$this->app->load_model("client_files");
					$obj_model_employee_detail->map_fields($data_t);
					$obj_model_employee_detail->execute("INSERT");
				}
		}
	//client sync end
		*/

	}
}
?>