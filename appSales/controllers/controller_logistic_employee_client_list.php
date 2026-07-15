<?
class _logistic_employee_client_list extends controller {
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

		$selectedEmployeeId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('selectedEmployeeId'));
		$selectedEmployeeId=$this->app->utility->decrypt($selectedEmployeeId);

		$whereCond=" and client_logistic_assign.logistic_manager_employee_id='".$selectedEmployeeId."'";
		if($employeeID!='' && $selectedEmployeeId!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (client.company_name LIKE '%$search%' or client.mobile LIKE '%$search%')";
			}

			$obj_model = $this->app->load_model("client_logistic_assign");
			$obj_model->join_table("client", "left", array(), array("client_id"=>"id"));
			$client_logistic_assign = $obj_model->execute("SELECT",false,"","client_logistic_assign.id!='' ".$whereCond."","client_logistic_assign.id desc");
			$count=count($client_logistic_assign);

			$limit=10;
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
			$obj_model_client->join_table("client_logistic_assign", "left", array(), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client_logistic_assign.id desc limit ".$start.",".$limit."");

			foreach($client as $item)
			{
				$address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'client','large');
				$myClientList[]=array(
					"id"=>$id,
					"name"=>$item['company_name'],
					"image"=>$image,
					"city"=>$address,
					"tagName"=>$item['client_status'],
					"tagColor"=>'#5ccdde'
				);
			}
			$result=["myEmployeeList"=>$myClientList];
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