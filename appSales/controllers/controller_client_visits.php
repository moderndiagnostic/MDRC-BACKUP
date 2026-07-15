<?
class _client_visits extends controller{
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
		
		if($employeeID!='' && $employeePhone!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'","employee.id desc limit 0,1");

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

			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');
			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');
			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');
			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');
			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');
			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');
			$monthVisits[]=array("id"=>'1',"name"=>'Octomber 2023',"count"=>'500');

			$result=["clientDetail"=>$clientDetail,"totalVisit"=>500,"monthVisits"=>$monthVisits];
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