<?
class _logistic_assign_client_daywise extends controller {
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

		$dayStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("dayStatus"));

		$latitude=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("latitude"));
		$longitude=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("longitude"));

		if($page>0) {
			$message=array("message"=>"No More Data Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (client.company_name LIKE '%$search%' or client.mobile LIKE '%$search%' or client.phone LIKE '%$search%' or city.name LIKE '%$search%' or client_detail.area LIKE '%$search%')";
			}
			$getDay=date('D');
			$getDay=$getDay=='Tue'?'Tues':$getDay;

			$today=$dayStatus==''?$getDay:$dayStatus;
			$dayList[]=["key"=>"Mon","value"=>"Mon","selected"=>$today=='Mon'?"Yes":"No"];
			$dayList[]=["key"=>"Tues","value"=>"Tues","selected"=>$today=='Tues'?"Yes":"No"];
			$dayList[]=["key"=>"Wed","value"=>"Wed","selected"=>$today=='Wed'?"Yes":"No"];
			$dayList[]=["key"=>"Thu","value"=>"Thu","selected"=>$today=='Thu'?"Yes":"No"];
			$dayList[]=["key"=>"Fri","value"=>"Fri","selected"=>$today=='Fri'?"Yes":"No"];
			$dayList[]=["key"=>"Sat","value"=>"Sat","selected"=>$today=='Sat'?"Yes":"No"];
			$dayList[]=["key"=>"Sun","value"=>"Sun","selected"=>$today=='Sun'?"Yes":"No"];

			$tempClientIds=[];
			if($getDay==$today){
				$obj_model = $this->app->load_model("employee_leave_assign_client");
				$tempClient = $obj_model->execute("SELECT",false,"","DATE(assign_date) = CURDATE() and employee_id='".$employeeID."'");
				if(count($tempClient)>0){
					
					foreach($tempClient as $item){
						$tempItems=explode(',',$item['client_ids']);
						foreach($tempItems as $i){
							if($i!=''){
							array_push($tempClientIds,$i);
							}
						}
					}
				}
			}
			if(count($tempClientIds)>0){
				$Cond="((client_logistic_assign.employee_id='".$employeeID."' and client.status='Active') or (FIND_IN_SET(`client`.`id`,'".implode(',',$tempClientIds)."')))";
			}
			else{
				$Cond="client_logistic_assign.employee_id='".$employeeID."' and client.status='Active'";
			}

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address","google_latitude","google_longitude"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_logistic_assign", "left", array("employee_id"), array("id"=>"client_id"));
			$obj_model_client->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"employee"], "left", array(), array("employee_id"=>"id"));
			$client = $obj_model_client->execute("SELECT",false,"",$Cond." ".$whereCond."","client.id desc");

			if(count($client)>0)
			{
				foreach($client as $item)
				{
					$days=$item['client_detail_sample_pickup']!=''?explode(',',$item['client_detail_sample_pickup']):[];
					if(in_array($today,$days) || ($dayStatus!='' && $dayStatus!='0' && in_array($dayStatus,$days)) || $dayStatus==0)
					{
						$address=$item['client_status']=='Client'?($item['client_detail_area'].' '.$item['city_name']):($item['client_address_google_city']??'');
						$id=$this->app->utility->encrypt($item['id']);
						$image=$this->app->utility->get_image_url($item["image"],'client','large');
						$employeeImage=$this->app->utility->get_image_url($item["employee_image"],'employee','large');
						
						if($item['client_address_google_latitude']!='') {
							$distance='Distance : '.$this->app->utility->getDistance($latitude,$longitude,$item['client_address_google_latitude'],$item['client_address_google_longitude'],'K');
						} else {
							$distance='Distance : N/A';
						}
						
						$logisticClientList[]=array(
							"id"=>$id,
							"companyName"=>$item['company_name'],
							"image"=>$image,
							"address"=>$address,
							"mobile"=>$item['mobile'],
							"frequency"=>'Frequency : '.$item['client_detail_sample_pickup_frequency'],
							"distance"=>$distance,
							"tagName"=>$item['client_status'],
							"tagColor"=>'#5ccdde',
							"primaryPerson"=>$item['employee_name']??'',
							"primaryImage"=>$employeeImage
						);
					}
				}
				$result=["logisticClientList"=>$logisticClientList,"dayList"=>$dayStatus!='0'?$dayList:[]];
				$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
			}
			else
			{
				$result=["logisticClientList"=>[],"dayList"=>[]];
				$message=array("message"=>"No Client Assigned.","msgCode"=>"0","result"=>$result);
			}
			
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