<?
class _logistic_assign_client_activity_update extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);

		$samplePickupID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("samplePickupID"));
		$samplePickupID=$samplePickupID!=''?$this->app->utility->decrypt($samplePickupID):'';

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action")); //checkIn, checkOut, meeting

		if($employeeID=='' || $action=='' || $clientID=='')
		{
			$message=array("message"=>"Data is missing.","msgcode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		
		if($action=='checkIn')
		{
			$dir_name =ABS_PATH.'/uploads/samplePickupUpdate/'.$employeeID;
			if (!is_dir($dir_name)) {
				mkdir($dir_name, 0777, true);
			}

			$latitude=$this->app->getPostVar('latitude');
			$longitude=$this->app->getPostVar('longitude');
			$address='';
			if(!empty($latitude) && !empty($longitude)){
				//Send request and receive json data by address
				$geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyDfpQaNzc-kOJJtSW30BanO-QDQjNI7wq0'); 
				$output = json_decode($geocodeFromLatLong);
				$status = $output->status;
				//Get address from json data
				$address = ($status=="OK")?$output->results[1]->formatted_address:'';
				if(!empty($address)){
					$addressArr=explode(',',$address);
					array_shift($addressArr);
					$address=implode(",",$addressArr);
				}
			}

			if($_FILES['checkInPhoto']['name']!='') {
				$image=$this->app->utility->FileUpload(['filename'=>$_FILES['checkInPhoto']['name'],'filetmpname'=>$_FILES['checkInPhoto']['tmp_name'],'folder'=>"samplePickupUpdate/".$employeeID]);
			}

			$data_t=array();		
			$data_t['employee_sample_pickup_id']=$samplePickupID;
			$data_t['employee_id']=$employeeID;
			$data_t['client_id']=$clientID;
			$data_t['pickup_status']='Check In';
			$data_t['pickup_date']=date('Y-m-d H:i:s');
			$data_t['updated_at']=date('Y-m-d H:i:s');
			$data_t['checkin_photo']=$image;
			$data_t['google_address']=$address;
			$data_t['device_type']=$deviceType;
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");
		
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else if($action=='startJourney')
		{
			if($samplePickupID=='')
			{
				$data_t=array();		
				$data_t['employee_id']=$employeeID;
				$data_t['client_id']=$clientID;
				$data_t['status']='Pending';
				$data_t['pickup_date']=date('d-m-Y');
				$data_t['created_at']=date('Y-m-d H:i:s');
				$data_t['updated_at']=date('Y-m-d H:i:s');
				$data_t['device_type']=$deviceType;
				$data_t['ip']=$_SERVER['REMOTE_ADDR'];
				$obj_model_employee=$this->app->load_model("employee_sample_pickup");
				$obj_model_employee->map_fields($data_t);
				$samplePickupID=$obj_model_employee->execute("INSERT");
			}

			$data_t=array();		
			$data_t['employee_sample_pickup_id']=$samplePickupID;
			$data_t['employee_id']=$employeeID;
			$data_t['client_id']=$clientID;
			$data_t['pickup_status']='Start Journey';
			$data_t['pickup_date']=date('Y-m-d H:i:s');
			$data_t['updated_at']=date('Y-m-d H:i:s');
			$data_t['device_type']=$deviceType;
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			//update task status
			$data_t=array();	
			$data_t['status']='In Progress';
			$obj_model_employee=$this->app->load_model("employee_sample_pickup");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$samplePickupID."'");
			
			$result=["samplePickupID"=>$this->app->utility->encrypt($samplePickupID)];
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='endJourney')
		{
			$data_t=array();	
			$data_t['employee_sample_pickup_id']=$samplePickupID;
			$data_t['employee_id']=$employeeID;
			$data_t['client_id']=$clientID;
			$data_t['pickup_status']='End Journey';
			$data_t['pickup_date']=date('Y-m-d H:i:s');
			$data_t['updated_at']=date('Y-m-d H:i:s');
			$data_t['device_type']=$deviceType;
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else if($action=='Payment')
		{
			$obj_model_employee_sample_pickup_update = $this->app->load_model("employee_sample_pickup_update");
			$pickup = $obj_model_employee_sample_pickup_update->execute("SELECT",false,"","pickup_status='Payment' and client_id='".$clientID."' and employee_id='".$employeeID."' and employee_sample_pickup_id='".$samplePickupID."'","employee_sample_pickup_update.id desc limit 0,1");
			if(count($pickup)>0 && $pickup[0]['collect_payment_otp_verify']=='No'){
				$pickup = $obj_model_employee_sample_pickup_update->execute("DELETE",false,"","pickup_status='Payment' and client_id='".$clientID."' and employee_id='".$employeeID."' and employee_sample_pickup_id='".$samplePickupID."'","");
			}

			$otp='';
			$collectPayment=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("collectPayment")); //Yes / No
			$paymentAmount=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentAmount")); //checkIn, checkOut, meeting
			$paymentMode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentMode"));
			if($collectPayment=='No')
			{
				$data_t=array();
				$data_t['employee_sample_pickup_id']=$samplePickupID;
				$data_t['employee_id']=$employeeID;
				$data_t['client_id']=$clientID;
				$data_t['pickup_status']='Payment';
				$data_t['pickup_date']=date('Y-m-d H:i:s');
				$data_t['updated_at']=date('Y-m-d H:i:s');
				$data_t['device_type']=$deviceType;
				$data_t['ip']=$_SERVER['REMOTE_ADDR'];
				$data_t['collect_payment_otp_verify']='Yes';
				$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("INSERT");
			}else {
				//send otp to customer
				$otp=4444;

				$data_t=array();
				$data_t['employee_sample_pickup_id']=$samplePickupID;
				$data_t['employee_id']=$employeeID;
				$data_t['client_id']=$clientID;
				$data_t['pickup_status']='Payment';
				$data_t['collect_payment']='Yes';
				$data_t['collect_payment_amount']=$paymentAmount;
				$data_t['pickup_date']=date('Y-m-d H:i:s');
				$data_t['updated_at']=date('Y-m-d H:i:s');
				$data_t['device_type']=$deviceType;
				$data_t['collect_payment_otp']=$otp;
				$data_t['ip']=$_SERVER['REMOTE_ADDR'];
				$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("INSERT");
			}
			$result=["otp"=>$otp];

			$message=array("message"=>"Updated Successfully.","msgCode"=>"1","result"=>$result);
		}
		else if($action=='Payment otp Verify')
		{
			$otp=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("otp")); //Yes / No
			
			$obj_model_employee_sample_pickup_update = $this->app->load_model("employee_sample_pickup_update");
			$pickup = $obj_model_employee_sample_pickup_update->execute("SELECT",false,"","pickup_status='Payment' and client_id='".$clientID."' and employee_id='".$employeeID."' and employee_sample_pickup_id='".$samplePickupID."'","id desc limit 0,1");

			if(count($pickup)>0) {
				if($otp==$pickup[0]['collect_payment_otp']) {
					$data_t=array();
					$data_t['collect_payment_otp_verify']='Yes';
					$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$pickup[0]['id']."'");

					$data_t=array();
					$data_t['collect_payment']='Yes';
					$data_t['payment_amount']=$pickup[0]['collect_payment_amount'];
					$obj_model_employee=$this->app->load_model("employee_sample_pickup");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$samplePickupID."'");

					$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
				} else {
					$message=array("message"=>"Wrong OTP.","msgCode"=>"0");
				}
			}else {
				$message=array("message"=>"Something gone Wrong.","msgCode"=>"0");
			}
		}
		else if($action=='Resend otp')
		{
			$obj_model_employee_sample_pickup_update = $this->app->load_model("employee_sample_pickup_update");
			$pickup = $obj_model_employee_sample_pickup_update->execute("SELECT",false,"","pickup_status='Payment' and client_id='".$clientID."' and employee_id='".$employeeID."' and employee_sample_pickup_id='".$samplePickupID."'","id desc limit 0,1");

			if(count($pickup)>0 && $pickup[0]['collect_payment_otp']!='') {
				//send otp
				$message=array("message"=>"OTP Sent Successfully.","msgCode"=>"1");
			}else {
				$message=array("message"=>"Something gone Wrong.","msgCode"=>"0");
			}
		}
		else if($action=='payment otp mobile')
		{
			$mobile=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("mobile")); 
			if($mobile!='' && $samplePickupID!='') 
			{
				$obj_model_employee_sample_pickup_update = $this->app->load_model("employee_sample_pickup_update");
				$pickup = $obj_model_employee_sample_pickup_update->execute("SELECT",false,"","pickup_status='Payment' and client_id='".$clientID."' and employee_id='".$employeeID."' and employee_sample_pickup_id='".$samplePickupID."'","id desc limit 0,1");

				if(count($pickup)>0 && $pickup[0]['collect_payment_otp']!='') {
					
					$data_t=array();
					$data_t['collect_payment_otp_mobile']=$mobile;
					$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
					$obj_model_employee->map_fields($data_t);
					$obj_model_employee->execute("UPDATE",false,"","id='".$pickup[0]['id']."'");

					$message=array("message"=>"OTP Sent Successfully.","msgCode"=>"1");
				}else {
					$message=array("message"=>"Something gone Wrong.","msgCode"=>"0");
				}
			} else {
				$message=array("message"=>"Please Enter Mobile No.","msgCode"=>"0");
			}
		}
		else if($action=='Sample Collect')
		{
			$sampletaken=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("sampletaken")); //Yes / No
			if($sampletaken=='No'){
				$data_t=array();
				$data_t['employee_sample_pickup_id']=$samplePickupID;
				$data_t['employee_id']=$employeeID;
				$data_t['client_id']=$clientID;
				$data_t['pickup_status']='Sample Collect';
				$data_t['pickup_date']=date('Y-m-d H:i:s');
				$data_t['updated_at']=date('Y-m-d H:i:s');
				$data_t['device_type']=$deviceType;
				$data_t['ip']=$_SERVER['REMOTE_ADDR'];
				$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("INSERT");
			}
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
		}
		else if($action=='Sample Collect List')
		{
			$sampletaken=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("sampletaken")); //Yes / No
			$list=[];

			$obj_model_client = $this->app->load_model("employee_sample_pickup_images");
			$itemList = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupID."'","IF(parent_id = 0, id, parent_id),parent_id != 0,id");
			//echo $obj_model_client->sql;exit;
			foreach($itemList as $item)
			{
				$image=$this->app->utility->get_image_url($item["image"],'samplePickup/'.$samplePickupID,'large');
				$list[]=[
					"id"=>$item['id'],
					"pickup_id"=>$item['master_no']>0?$item['master_no']:'',
					"parent_id"=>$item['parent_id']>0?$item['parent_id']:'',
					"number"=>$item['id'],
					"image"=>$image,
					"barcode"=>$item['barcode'],
					"remark"=>$item["remark"]??'',
					"date"=>date('d-m-Y h:i A',strtotime($item['updated_at']))
				];
			}
			$result=["list"=>$list];
			$message=array("message"=>"List","msgCode"=>"1","result"=>$result);
		}
		else if($action=='Upload' || $action=='upload')
		{
			$barcode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("barcode")); //Yes / No
			$parent_id=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("parent_id"));
			
			$dir_name =ABS_PATH.'/uploads/samplePickup/'.$samplePickupID;
			if (!is_dir($dir_name)) {
				mkdir($dir_name, 0777, true);
			}

			if($_FILES['image']['name']!='') {
				$image=$this->app->utility->FileUpload(['filename'=>$_FILES['image']['name'],'filetmpname'=>$_FILES['image']['tmp_name'],'folder'=>"samplePickup/".$samplePickupID]);
			}

			$master_no=0;
			if(empty($parent_id)) {
				$obj_model_client = $this->app->load_model("employee_sample_pickup_images");
				$last_no = $obj_model_client->execute("SELECT",false,"","master_no > 0","id desc limit 0,1");
				$master_no = $last_no[0]['master_no'] + 1;
			}
			else
			{
				$obj_model_client = $this->app->load_model("employee_sample_pickup_images");
				$last_no = $obj_model_client->execute("SELECT",false,"","id='".$parent_id."'","");
				$master_no = $last_no[0]['master_no'];
			}


			$data_t=array();
			$data_t['employee_sample_pickup_id']=$samplePickupID;
			$data_t['employee_id']=$employeeID;
			$data_t['parent_id']=$parent_id??NULL;
			$data_t['client_id']=$clientID;
			$data_t['master_no']=$master_no;
			$data_t['image']=$image;
			$data_t['barcode']=$barcode;
			$data_t['updated_at']=date('Y-m-d H:i:s');
			$data_t['device_type']=$deviceType;
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_images");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");

			$obj_model_client = $this->app->load_model("employee_sample_pickup_images");
			$itemList = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupID."'","");
			if(count($itemList)==1)
			{
				$data_t=array();
				$data_t['employee_sample_pickup_id']=$samplePickupID;
				$data_t['employee_id']=$employeeID;
				$data_t['client_id']=$clientID;
				$data_t['pickup_status']='Sample Collect';
				$data_t['collect_sample']='Yes';
				$data_t['pickup_date']=date('Y-m-d H:i:s');
				$data_t['updated_at']=date('Y-m-d H:i:s');
				$data_t['device_type']=$deviceType;
				$data_t['ip']=$_SERVER['REMOTE_ADDR'];
				$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("INSERT");

				$data_t=array();
				$data_t['collect_sample']='Yes';
				$obj_model_employee=$this->app->load_model("employee_sample_pickup");
				$obj_model_employee->map_fields($data_t);
				$obj_model_employee->execute("UPDATE",false,"","id='".$samplePickupID."'");
			}

			$message=array("message"=>"List","msgCode"=>"1");
		}
		else if($action=='Remark' || $action=='remark')
		{
			$id=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("id"));
			$remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("remark"));
			$barcode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("barcode"));
			
			$data_t=array();
			$image='';
			if($_FILES['image']['name']!='') {
				$image=$this->app->utility->FileUpload(['filename'=>$_FILES['image']['name'],'filetmpname'=>$_FILES['image']['tmp_name'],'folder'=>"samplePickup/".$samplePickupID]);
			}

			$data_t['remark']=$remark;
			$data_t['barcode']=$barcode;
			if($image!='') {
				$data_t['image']=$image;
			}
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_images");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$id."' and employee_sample_pickup_id='".$samplePickupID."' and employee_id='".$employeeID."'");

			$message=array("message"=>"success","msgCode"=>"1");
		}
		else if($action=='Sample Delete')
		{
			$sampleID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("sampleID")); //Yes / No
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_images");
			$obj_model_employee->execute("DELETE",false,"","id='".$sampleID."' OR parent_id='".$sampleID."'");

			$message=array("message"=>"Deleted Successfully.","msgCode"=>"1");
		}
		else if($action=='checkOut')
		{
			
			$data_t=array();
			$data_t['employee_sample_pickup_id']=$samplePickupID;
			$data_t['employee_id']=$employeeID;
			$data_t['client_id']=$clientID;
			$data_t['pickup_status']='Check Out';
			$data_t['pickup_date']=date('Y-m-d H:i:s');
			$data_t['updated_at']=date('Y-m-d H:i:s');
			$data_t['device_type']=$deviceType;
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT");
			
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_update");
			$checkUpdate=$obj_model_employee->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupID."' and pickup_status='Start Journey'","id desc");

			//get start journey lat long
			$checkin_latitude=$checkUpdate[0]['latitude'];
			$checkin_longitude=$checkUpdate[0]['longitude'];
			$checkout_latitude=$this->app->getPostVar('latitude');
			$checkout_longitude=$this->app->getPostVar('longitude');
			$distance='';

			if(!empty($checkout_latitude) && !empty($checkout_longitude) && !empty($checkin_latitude) && !empty($checkin_longitude))
			{
 				$apiKey = GOOGLE_MAP_API_KEY;
				$directionsUrl = "https://maps.googleapis.com/maps/api/directions/json?origin=$checkin_latitude,$checkin_longitude&destination=$checkout_latitude,$checkout_longitude&key=$apiKey";
        
				$response = file_get_contents($directionsUrl);
				$data = json_decode($response, true);
		
				// Extract distance in KM
				if (!empty($data['routes'][0]['legs'][0]['distance']['text'])) {
					$distance = $data['routes'][0]['legs'][0]['distance']['text'];
				}
			}

			$obj_model = $this->app->load_model("employee_sample_pickup_images");
			$pickup_images = $obj_model->execute("SELECT",false,"","employee_sample_pickup_id='".$samplePickupID."'");

			//update task status
			$data_t=array();	
			$data_t['sample_count']=count($pickup_images);
			$data_t['status']='Completed';
			$data_t['distance_km']=$distance;
			$obj_model_employee=$this->app->load_model("employee_sample_pickup");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$samplePickupID."'");
			

			

			$obj_model_client = $this->app->load_model("employee");
			$obj_model_client->join_table("employee_centre", "left", array(), array("id"=>"employee_id"));
			$obj_model_client->join_table(["employee_centre"=>"employee_centre","master_centre"=>"master_centre"], "left", array(), array("lms_centre_id"=>"lms_center_id"));
			$employee = $obj_model_client->execute("SELECT",false,"","employee.id='".$pickup_images[0]['employee_id']."'");

			$obj_model_client = $this->app->load_model("client");
			$client = $obj_model_client->execute("SELECT",false,"","id='".$pickup_images[0]['client_id']."'");


			$employeeData=[
				"code"=>$employee[0]['lms_employee_code']??'',
				"name"=>$employee[0]['name']??'',
				"mobile"=>$employee[0]['mobile']??'',
				"center"=>$employee[0]['master_centre_name']??'',
				"center_id"=>$employee[0]['master_centre_lms_center_id']??'',
				"client"=>$client[0]['company_name']??'',
				"client_id"=>$client[0]['panel_id']>0?$client[0]['panel_id']:'',
			];

			foreach ($pickup_images as $item) {
				
				$list=[];
				if (empty($item['parent_id']) || $item['parent_id'] == 0) {
					if (!empty($item['barcode'])) {
						$image = $this->app->utility->get_image_url($item["image"], 'samplePickup/' . $samplePickupID, 'large');
						$list[] = [
							"image" => $image,
							"barcode" => $item['barcode'],
							"remark" => $item["remark"] ?? '',
							"date" => date('d-m-Y h:i A', strtotime($item['updated_at']))
						];

					
						foreach ($pickup_images as $childItem) {
							if ($childItem['parent_id'] == $item['id'] && !empty($childItem['barcode'])) {
								$image = $this->app->utility->get_image_url($childItem["image"], 'samplePickup/' . $samplePickupID, 'large');
								$list[] = [
									"image" => $image,
									"barcode" => $childItem['barcode'],
									"remark" => $childItem["remark"] ?? '',
									"date" => date('d-m-Y h:i A', strtotime($childItem['updated_at']))
								];
							}
						}

						if(count($list)>0) {
							$payload = [
								'message'=>'Success',
								'msgCode'=>'1',
								'result' => [
									'list' => $list,
									'employee' => $employeeData
								],
							];

							$json_data = json_encode($payload);
							$ch = curl_init('https://lis6.mdrcindia.com/MDRCNEW/api/MDRCLisApi/SaveLogisticDetail'); 
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
							curl_setopt($ch, CURLOPT_HTTPHEADER, [
								'Content-Type: application/json'
							]);
							$response = curl_exec($ch);

							if(curl_errno($ch)) {
								echo 'Error: ' . curl_error($ch); 
							}
							curl_close($ch);

							$data_t=array();	
							$data_t['employee_sample_pickup_payment_id']=0;
							$data_t['request_json']=json_encode($payload);
							$data_t['response_json']=$response;
							$data_t['ip']='';
							$data_t['created_at']='';
							$obj_model_employee=$this->app->load_model("employee_sample_pickup_payment_lis_calls");
							$obj_model_employee->map_fields($data_t);
							$obj_model_employee->execute("INSERT",false,"","");
						}
					}
				}
			}
			$message=array("message"=>"Updated Successfully.","msgCode"=>"1");
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