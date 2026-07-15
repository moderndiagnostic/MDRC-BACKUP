<?
class _task_client_select_list extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$taskId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("taskId"));
		$taskId=$this->app->utility->decrypt($taskId);
		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		//$page=!empty($page)?(int)$page:0;
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		
		$taskSelectedClient=[];
		$whereCond='';

		if($employeeID!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (client.company_name LIKE '%$search%' or client.phone LIKE '%$search%')";
			}

			if($page==0 && $taskId!='') {
				//edit task
				$obj_model_employee_task_master = $this->app->load_model("employee_task_master");
				$employee_task_master = $obj_model_employee_task_master->execute("SELECT",false,"","id='".$taskId."'");
				array_push($taskSelectedClient,$this->app->utility->encrypt($employee_task_master[0]['client_id']));
			}

			$obj_model_employee = $this->app->load_model("client");
			$employee = $obj_model_employee->execute("SELECT",false,"","status='Active' ".$whereCond."","client.id desc");
			$count=count($employee);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Employee Found.","msgCode"=>"0");
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
			$result=["clientList"=>$clientList,"taskSelectedClient"=>$taskSelectedClient,"taskSelectedClientString"=>implode(',',$taskSelectedClient)];
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