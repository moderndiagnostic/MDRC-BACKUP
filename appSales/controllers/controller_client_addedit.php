<?
class _client_addedit extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$clientId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("clientId"));
		$clientId=$this->app->utility->decrypt($clientId);

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('action'));
		
		if($employeeID=='' || $action=='' && $employeePhone=='') {
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$whereCond='';
		if($clientId!='') {
			$whereCond.="and client.id='".$clientId."'";
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
		} else {
			$client=[];
		}

		if($action=='create_client')
		{
			$name=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('name'));
			$email=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('email'));
			$mobile=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('mobile'));
			$specialization=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('specialization'));
			$businessType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('businessType'));
			$legalType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('legalType'));
			$clientAddAction=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientAddAction'));
			$registerType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('registerType'));

			/* if($mobile=='' ) {
				$message=array("message"=>"Please enter required data.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			} */
			if($name=='' ) {
				$message=array("message"=>"Please enter company name.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			if($mobile!='' ) {
				$whereCond=$clientId!=''?" and client.id!='".$clientId."'":"";
				$whereCond.=$email!=''?" and (client.mobile='".$mobile."' || client.email='".$email."')":" and client.mobile='".$mobile."'";
				$obj_model_client_check = $this->app->load_model("client");
				$checkClient = $obj_model_client_check->execute("SELECT",false,"","client.status!='Trash' ".$whereCond."","client.id desc limit 0,1");
				if(count($checkClient)>0) {
					$message=array("message"=>"Email or phone already exist. ","msgCode"=>"0");
					$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
					echo $this->app->utility->indent($opt); exit;
				}
			}

			//check if client is new or task is edit
			if($clientId!='') {
				//update
				$data_t=array();
				$data_t['company_name']=$name;
				$data_t['mobile']=$mobile;
				$data_t['email']=$email;
				$data_t['status']='Active';
				$data_t['client_status']=$clientAddAction;
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$obj_model_client=$this->app->load_model("client");
				$obj_model_client->map_fields($data_t);
				$obj_model_client->execute("UPDATE",false,"","id='".$clientId."'");		

				$data_t=array();
				$data_t['business_type']=$businessType;
				$data_t['specialization']=$specialization;
				$data_t['compnay_legal']=$legalType;
				$data_t['register_type']=$registerType;
				$obj_model_table=$this->app->load_model("client_detail");
				$obj_model_table->map_fields($data_t);
				$obj_model_table->execute("UPDATE",false,"","client_id='".$clientId."'");		
				$message='Client / Field Visit Details Updated.';
			} else {
				//insert
				$data_t=array();
				$data_t['company_name']=$name;
				$data_t['mobile']=$mobile;
				$data_t['email']=$email;
				$data_t['status']='Active';
				$data_t['client_status']=$clientAddAction;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$obj_model_client=$this->app->load_model("client");
				$obj_model_client->map_fields($data_t);
				$clientId=$obj_model_client->execute("INSERT",false,"","");

				$data_t=array();
				$data_t['client_id']=$clientId;
				$data_t['business_type']=$businessType;
				$data_t['specialization']=$specialization;
				$data_t['compnay_legal']=$legalType;
				$data_t['register_type']=$registerType;
				$data_t['added_by_employee_id']=$employeeID;
				$obj_model_table=$this->app->load_model("client_detail");
				$obj_model_table->map_fields($data_t);
				$obj_model_table->execute("INSERT",false,"","");

				$data_t=array();
				$data_t['client_id']=$clientId;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$obj_model_table=$this->app->load_model("client_bank");
				$obj_model_table->map_fields($data_t);
				$obj_model_table->execute("INSERT",false,"","");

				$data_t=array();
				$data_t['client_id']=$clientId;
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$obj_model_table=$this->app->load_model("client_address");
				$obj_model_table->map_fields($data_t);
				$obj_model_table->execute("INSERT",false,"","");

				$data_t=array();
				$data_t['client_id']=$clientId;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$data_t['updated_at']=date("Y-m-d H:i:s");
				$obj_model_table=$this->app->load_model("client_files");
				$obj_model_table->map_fields($data_t);
				$obj_model_table->execute("INSERT",false,"","");
				$message='Client / Field Visit Added.';
			}
	
			$result=["clientId"=>$this->app->utility->encrypt($clientId)];
			$message=array("message"=>$message,"msgCode"=>"1","result"=>$result);
		}
		else if($action=='client_location_update')
		{
			if($clientId=='') {
				$message=array("message"=>"Try Again.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$latitude=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('latitude'));
			$longitude=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('longitude'));
			$googleFullAddress=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('googleFullAddress'));
			$city=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('city'));
			$pincode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('pincode'));

			$data_t=array();
			$data_t['google_address']=$googleFullAddress;
			$data_t['google_latitude']=$latitude;
			$data_t['google_longitude']=$longitude;
			$data_t['google_pincode']=$pincode;
			$data_t['google_city']=$city;
			$data_t['updated_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_address");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","client_id='".$clientId."'");		

			$result=["clientId"=>$this->app->utility->encrypt($clientId)];
			$message=array("message"=>"Client / Field Visit Details Updated.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='client_files_update')
		{
			if($clientId=='') {
				$message=array("message"=>"Try Again.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$dir_name =ABS_PATH.'/uploads/clientFile/'.$clientId;
			if (!is_dir($dir_name)) {
				mkdir($dir_name, 0777, true);
			}

			$fileType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('fileType'));
			$fileNo=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('fileNo'));
			
			if($_FILES['photo1']['name']!='') {
				$image1=$this->app->utility->FileUpload(['filename'=>$_FILES['photo1']['name'],'filetmpname'=>$_FILES['photo1']['tmp_name'],'folder'=>"clientFile/".$clientId]);
			}
			if($_FILES['photo2']['name']!='') {
				$image2=$this->app->utility->FileUpload(['filename'=>$_FILES['photo2']['name'],'filetmpname'=>$_FILES['photo2']['tmp_name'],'folder'=>"clientFile/".$clientId]);
			}	

			$data_t=array();
			if($fileType=='company_photo') {
			$data_t['company_photo']=$image1;
			}
			if($fileType=='aadhar_card') {
			$data_t['aadhar_card_no']=$fileNo;
			$data_t['aadhar_card_front_photo']=$image1;
			$data_t['aadhar_card_back_photo']=$image2;
			}
			if($fileType=='pancard') {
			$data_t['pancard_no']=$fileNo;
			$data_t['pancard_photo']=$image1;
			}
			if($fileType=='incorporation_photo') {
			$data_t['incorporation_photo']=$image1;
			}
			if($fileType=='registration_photo') {
			$data_t['registration_photo']=$image1;
			}
			if($fileType=='gst') {
			$data_t['gst_no']=$fileNo;
			$data_t['gst_photo']=$image1;
			}
			if($fileType=='sign_photo') {
			$data_t['sign_photo']=$image1;
			}
			$data_t['updated_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_files");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","client_id='".$clientId."'");		

			$result=["clientId"=>$this->app->utility->encrypt($clientId)];
			$message=array("message"=>"Location Updated.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='create_client_bank_update')
		{
			if($clientId=='') {
				$message=array("message"=>"Try Again.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}
			$data_t=array();
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client_bank");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","client_id='".$clientId."'");		

			$result=["clientId"=>$this->app->utility->encrypt($clientId)];
			$message=array("message"=>"Client created.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='create_client_other_update')
		{
			if($clientId=='') {
				$message=array("message"=>"Try Again.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$sample_pickup=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('sample_pickup'));
			$sample_pickup_frequency=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('sample_pickup_frequency'));
			$payment_mode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('payment_mode'));
			$invoice_billing_cycle=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('invoice_billing_cycle'));
			$request_for_client=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('request_for_client'));

			if($request_for_client=='Yes'){
				$data_t=array();
				$data_t['client_status']='Request for Client';
				$obj_model_client=$this->app->load_model("client");
				$obj_model_client->map_fields($data_t);
				$obj_model_client->execute("UPDATE",false,"","id='".$clientId."'");	
			}

			$data_t=array();
			$obj_model_client=$this->app->load_model("client_detail");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","client_id='".$clientId."'");		

			$result=["clientId"=>$this->app->utility->encrypt($clientId)];
			$message=array("message"=>"Client Updated.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='client_delete')
		{
			if($clientId=='') {
				$message=array("message"=>"Try Again.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			if($client['client_status']!='Field Visit') {
				$message=array("message"=>"You can not delete this. Ask Admin.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			$data_t['status']='Trash';
			$obj_model_client=$this->app->load_model("client");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$clientId."'");		

			$message=array("message"=>"Client Deleted.","msgCode"=>"1");
		}
		else if($action=='client_request_for_lms')
		{
			if($clientId=='') {
				$message=array("message"=>"Try Again.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			if($client['client_status']!='Field Visit') {
				$message=array("message"=>"...","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			$data_t['client_status']='Request for Client';
			$obj_model_client=$this->app->load_model("client");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$clientId."'");		

			$message=array("message"=>"Client Requst.","msgCode"=>"1");
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