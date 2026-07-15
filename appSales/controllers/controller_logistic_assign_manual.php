<?
class _logistic_assign_manual extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$name=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('name'));
		$email=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('email'));
		$mobile=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('mobile'));
		$businessType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('businessType'));
		$city=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('city'));
		$city=$this->app->utility->decrypt($city);
		$state=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('state'));
		$state=$this->app->utility->decrypt($state);
		$address=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('address'));
		$pincode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('pincode'));
		
		if($name=='' ) {
			$message=array("message"=>"Please enter company name.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		else
		{
			$data_t=array();
			$data_t['company_name']=$name;
			$data_t['mobile']=$mobile;
			$data_t['email']=$email;
			$data_t['city_id']=$city;
			$data_t['state_id']=$state;
			$data_t['status']='Active';
			$data_t['client_status']='Field Visit';
			$data_t['created_at']=date("Y-m-d H:i:s");
			$data_t['updated_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("client");
			$obj_model_client->map_fields($data_t);
			$clientId=$obj_model_client->execute("INSERT",false,"","");
			

			$data_t=array();
			$data_t['client_id']=$clientId;
			$data_t['business_type']=$businessType;
			$data_t['address']=$address.' - '.$pincode;
			$data_t['added_by_employee_id']=$employeeID;
			$obj_model_table=$this->app->load_model("client_detail");
			$obj_model_table->map_fields($data_t);
			$obj_model_table->execute("INSERT",false,"","");

			if($clientId!='')
			{
				$data_t=array();		
				$data_t['employee_id']=$employeeID;
				$data_t['client_id']=$clientId;
				$data_t['status']='Pending';
				$data_t['pickup_type']='manual';
				$data_t['pickup_date']=date('d-m-Y');
				$data_t['created_at']=date('Y-m-d H:i:s');
				$data_t['updated_at']=date('Y-m-d H:i:s');
				$data_t['device_type']=$deviceType;
				$data_t['ip']=$_SERVER['REMOTE_ADDR'];
				$obj_model_employee=$this->app->load_model("employee_sample_pickup");
				$obj_model_employee->map_fields($data_t);
				$samplePickupID=$obj_model_employee->execute("INSERT");
				$sampleID=$this->app->utility->encrypt($samplePickupID);
				$client_Id=$this->app->utility->encrypt($clientId);
			}
			if($samplePickupID!='')
			{
				$result=array("clientId"=>$client_Id,"sampleId"=>$sampleID);
				$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
			}
			else
			{
				$result=array("clintId"=>'',"sampleId"=>'');
				$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0","result"=>$result);
			}
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>