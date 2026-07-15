<?
class _client_visits_monthly extends controller{
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

		$month=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("month"));
		
		if($employeeID!='' && $employeePhone!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employeeDetailList = $obj_model_employee->execute("SELECT",false,"","employee.id='".$employeeID."'","employee.id desc limit 0,1");
			if(count($employeeDetailList)>0){
				$employee=$employeeDetailList[0];
				$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
				$employeeDetail=[
					"heading"=>'Sales Person Tagged',
					"name"=>$employee['name'],
					"image"=>$image,
					"detail"=>$employee['master_designation_name'],
					"mobile"=>$employee['mobile']
				];
			}

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_city","google_latitude","google_longitude"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.id='".$clientID."' ","client.id desc limit 0,1");
			$item=$client[0];

			$address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
			$id=$this->app->utility->encrypt($item['id']);
			$image=$this->app->utility->get_image_url($item["image"],'client','large');
			$clientDetail=array(
				"id"=>$id,
				"companyName"=>$item['company_name'],
				"mobile"=>$item['mobile'],
				"image"=>$image,
				"address"=>$address,
				"latitude"=>$item['client_address_google_latitude'],
				"longitude"=>$item['client_address_google_longitude'],
				"tagName"=>$item['client_status'],
				"tagColor"=>'#5ccdde'
			);

			$dayVisits[]=array("id"=>'1',"date"=>'Octomber 2023',"count"=>'500');
			$dayVisits[]=array("id"=>'1',"date"=>'Octomber 2023',"count"=>'500');
			$dayVisits[]=array("id"=>'1',"date"=>'Octomber 2023',"count"=>'500');
			$dayVisits[]=array("id"=>'1',"date"=>'Octomber 2023',"count"=>'500');
			$dayVisits[]=array("id"=>'1',"date"=>'Octomber 2023',"count"=>'500');
			
			$result=["monthNext"=>$month,"monthPrevious"=>$month,"clientDetail"=>$clientDetail,"employeeDetail"=>$employeeDetail,"totalVisit"=>500,"currentMonthName"=>'Oct 2023',"dayVisits"=>$dayVisits];
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