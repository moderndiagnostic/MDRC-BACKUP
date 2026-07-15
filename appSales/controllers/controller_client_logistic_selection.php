<?
class _client_logistic_selection extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);

		$clientId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientId'));
		$clientId=$this->app->utility->decrypt($clientId);
		
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		//$page=!empty($page)?(int)$page:0;
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));

		$obj_model_current = $this->app->load_model("employee");
		$obj_model_current->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
		$currentEmployee = $obj_model_current->execute("SELECT",false,"","employee.id='".$employeeID."'");

		$whereCond='';
		if($employeeID!='' && $deviceType!='' && $action=='client')
		{
			if($search!='') {
				$whereCond.=" and (client.company_name LIKE '%$search%' or client.phone LIKE '%$search%')";
			}

			$obj_model_employee = $this->app->load_model("client");
			$employee = $obj_model_employee->execute("SELECT",false,"","status='Active' ".$whereCond."","client.id desc");
			$count=count($employee);

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
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc limit ".$start.",".$limit."");

			foreach($client as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'client','large');
				$address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$clientList[]=array(
					"id"=>$id,
					"companyName"=>$item['company_name'],
					"image"=>$image,
					"address"=>$address,
					"tagName"=>$item['client_status'],
					"tagColor"=>'#5ccdde'
					);
			}
			$result=["clientList"=>$clientList];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($employeeID!='' && $deviceType!='' && $action=='employee')
		{
			$viewClient='';
			if($clientId=='')
			{
				//client_logistic_assign
				$message=array("message"=>"Client Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model = $this->app->load_model("client_logistic_assign");
			$logistic_assign = $obj_model->execute("SELECT",false,"","client_id='".$clientId."'");
			if(count($logistic_assign)<=0)
			{
				$showClient=true;
				$whereCond.=" and (master_designation.name='Logistics Manager')";
			}
			else
			{
				$showClient=false;
				$obj_model = $this->app->load_model("employee");
				$manager = $obj_model->execute("SELECT",false,"","id='".$logistic_assign[0]['logistic_manager_employee_id']."'");
				if($manager[0]['lms_employee_id']==0)
				{
					$message=array("message"=>"No Employee Found.","msgCode"=>"0");
					$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
					echo $this->app->utility->indent($opt); exit;
				}
				$whereCond.=" and (employee.reporting_employee_lms_id='".$manager[0]['lms_employee_id']."')";
			}

			if($search!='') {
				$whereCond.=" and (employee.name LIKE '%$search%' or employee.mobile LIKE '%$search%')";
			}

			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.status='Active' ".$whereCond."","employee.id desc");
			$count=count($employee);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Employee Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$obj_model_employee->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.status='Active' ".$whereCond."","employee.id desc limit ".$start.",".$limit."");

			foreach($employee as $item)
			{
				if($showClient)
				{
					//check how many clients assign to manager
					$obj_model = $this->app->load_model("client_logistic_assign");
					$logistic_assign = $obj_model->execute("SELECT",false,"SELECT count(id) as totalC from client_logistic_assign where logistic_manager_employee_id='".$item['id']."'");
					$viewClient=$logistic_assign[0]['totalC'].' Clients Assigned';
				} else {
					$viewClient='';
				}
					
				$item['city_name']=!empty($item['city_name'])?$item['city_name']:"";
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'employee','large');
				$employeeList[]=array("id"=>$id,"name"=>$item['name'],"image"=>$image,"designation"=>$item['master_designation_name'],"city"=>$item['city_name'],"viewClient"=>$viewClient);
			}
			
			$result=["employeeList"=>$employeeList];
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