<?
class _push_test extends controller {
	function init() {
	}

	function onload()
	{
		$obj_table = $this->app->load_model("employee_task_master_update");
		$result = $obj_table->execute("SELECT", false, "", "activity='Check In' and google_address=''","employee_task_master_update.id desc limit 0,1");
		
		if(count($result)>0) {
			$latitude=$result[0]['latitude'];
			$longitude=$result[0]['longitude'];
			if(!empty($latitude) && !empty($longitude)){
				//Send request and receive json data by address
				$geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyDfpQaNzc-kOJJtSW30BanO-QDQjNI7wq0'); 
				$output = json_decode($geocodeFromLatLong);
				
				$status = $output->status;
				//Get address from json data
				$address = ($status=="OK")?$output->results[0]->formatted_address:'';
				if(!empty($address)){
					$addressArr=explode(',',$address);
					array_shift($addressArr);
					$address=implode(",",$addressArr);
				}
				//Return address of the given latitude and longitude
				if(!empty($address)){
					$data_t=array();
					$data_t['google_address']=$address;
					$obj_model=$this->app->load_model("employee_task_master_update");
					$obj_model->map_fields($data_t);
					$obj_model->execute("UPDATE",false,"","activity='Check In' and id='".$result[0]['id']."'");
				}
			}
		}

		/* $employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$notificationType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('notificationType'));
		
		if($employeeID!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'");

			if(count($employee)>0)
			{
				$data=[
					'notificationType'=>$notificationType,
					'title'=>'Test push',
					'image'=>'https://www.mdrcindia.com/uploads/main_banner_images/7651024eb2ff23a31fe2475aedbe5a39c89e81.jpg',
					'message'=>'Test Push Message',
					'body'=>'Test Push Message',
					'clientID'=>'MQ==',
					'employeeID'=>'MQ==',
					'taskID'=>'MQ==',
					'click_action'=>'OffersActivity'
				];

				$to = array();
				foreach($employee as $item)
				{
					array_push($to, $item['fcm_token']);
				}
				$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
				$fields = array(
				'registration_ids' => $to,
				'priority'=> 'high',
				//'notification' => $data,
				'data' => $data
				);

				define("SERVER_KEY", 'AAAABxpd1d0:APA91bG6IKvZzI_JMT63mNmzRoAVyMsd44Pn7wEGSg3rNW5kyThvby0YlWWmrZ7Un-wclu_Uln1UB_40txyuORIn79-aXdRdbFp98Qdbs-5xpxEu9F2-1ueY5o4_rSSZr9lcAfodbe23');

				$headers = array(
				'Authorization:key=' . SERVER_KEY,
				'Content-Type:application/json'
				);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result = curl_exec($ch);
				curl_close($ch);
				print_r($result); exit;
				$message=array("message"=>"Password Reset Successfully.","msgCode"=>"1");
			}
			else
			{
				$message=array("message"=>"Please Enter Correct old Password.","msgCode"=>"0");
			}
			
		}
		else
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit; */
	}
}
?>