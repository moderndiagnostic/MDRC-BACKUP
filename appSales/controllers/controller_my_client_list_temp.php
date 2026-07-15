<?
class _my_client_list_temp extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		//$page=!empty($page)?(int)$page:0;
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));

		$clientStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("clientStatus"));
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'","employee.id desc limit 0,1");

			if($clientStatus!='') {
				$whereCond.=" and client.client_status='".$clientStatus."'";
			}

			$whereCond.=" and (client.lms_employee_id='".$employee[0]['lms_employee_id']."' || client_detail.added_by_employee_id='".$employeeID."')";

			if($search!='') {
				$whereCond.=" and (client.company_name LIKE '%$search%' or client.mobile LIKE '%$search%' or client.phone LIKE '%$search%' or city.name LIKE '%$search%' or client_detail.area LIKE '%$search%')";
			}

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc");
			$count=count($client);

			if($page==0) {
			//count start
			$query="SELECT COUNT(CASE WHEN client.status != '' THEN 1 END) AS AllCount, COUNT(CASE WHEN client.client_status = 'Client' THEN 1 END) AS ClientCount, COUNT(CASE WHEN client.client_status = 'Field Visit' THEN 1 END) AS FieldCount, COUNT(CASE WHEN client.client_status = 'Request for Client' THEN 1 END) AS RequestClient FROM client LEFT JOIN client_detail on client.id=client_detail.client_id where (client.lms_employee_id='".$employee[0]['lms_employee_id']."' || client_detail.added_by_employee_id='".$employeeID."') and client.status!='Trash'";
			
			$obj_model_client = $this->app->load_model("client");
			$countQuery = $obj_model_client->execute("SELECT",false,$query);

			$clientStatusList[]=["key"=>"","value"=>"All (".$countQuery[0]['AllCount'].")"];
			$clientStatusList[]=["key"=>"Client","value"=>"Client (".$countQuery[0]['ClientCount'].")"];
			$clientStatusList[]=["key"=>"Request For Client","value"=>"IT Approval Request (".$countQuery[0]['RequestClient'].")"];
			$clientStatusList[]=["key"=>"Field Visit","value"=>"Field Visit (".$countQuery[0]['FieldCount'].")"];
			} else {
				$clientStatusList=array();
			}

			$limit=50;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Client Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc limit ".$start.",".$limit."");

			foreach($client as $item)
			{
				$address=$item['client_status']=='Client'?($item['client_detail_area'].' '.$item['city_name']):($item['client_address_google_city']??'');
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'client','large');

				$logisticClientList[]=array(
					"id"=>$id,
					"companyName"=>$item['company_name'],
					"image"=>$image,
					"address"=>$address,
					"mobile"=>$item['mobile'],
					"frequency"=>'Frequency : '.$item['client_detail_sample_pickup_frequency'],
					"distance"=>'',
					"tagName"=>$item['client_status'],
					"tagColor"=>'#5ccdde',
					"primaryPerson"=>$item['employee_name']??'',
					"primaryImage"=>''
				);

				// $myClientList[]=array(
				// 	"id"=>$id,
				// 	"companyName"=>$item['company_name'],
				// 	"image"=>$image,
				// 	"address"=>$address,
				// 	"tagName"=>$item['client_status'],"tagColor"=>'#5ccdde'
				// );
			}

			$today='';
			$dayList[]=["key"=>"Mon","value"=>"Mon","selected"=>$today=='Mon'?"Yes":"No"];
			$dayList[]=["key"=>"Tues","value"=>"Tues","selected"=>$today=='Tues'?"Yes":"No"];
			$dayList[]=["key"=>"Wed","value"=>"Wed","selected"=>$today=='Wed'?"Yes":"No"];
			$dayList[]=["key"=>"Thu","value"=>"Thu","selected"=>$today=='Thu'?"Yes":"No"];
			$dayList[]=["key"=>"Fri","value"=>"Fri","selected"=>$today=='Fri'?"Yes":"No"];
			$dayList[]=["key"=>"Sat","value"=>"Sat","selected"=>$today=='Sat'?"Yes":"No"];
			$dayList[]=["key"=>"Sun","value"=>"Sun","selected"=>$today=='Sun'?"Yes":"No"];

			$result=["logisticClientList"=>$logisticClientList,"dayList"=>$dayList];
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