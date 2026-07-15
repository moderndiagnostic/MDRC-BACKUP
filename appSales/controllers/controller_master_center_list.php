<?
class _master_center_list extends controller {
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
		$search=strtolower(mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search")));
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (LOWER(master_centre.name) LIKE '%$search%' or LOWER(master_centre.address) LIKE '%$search%')";
			}

			$obj_model_master_centre = $this->app->load_model("master_centre");
			$obj_model_master_centre->set_fields_to_get("name");
			$master_centre = $obj_model_master_centre->execute("SELECT",false,"","master_centre.employee_id='".$employeeID."'","");	
			$center_ids=implode(',', array_column($master_centre, 'id'));
		
			$whereCond.=" and (id NOT IN (".$center_ids."))";

			$obj_model = $this->app->load_model("master_centre");
			$obj_model->set_fields_to_get("id");
			$employee = $obj_model->execute("SELECT",false,"","status='Active' ".$whereCond."","master_centre.id desc");
			$count=count($employee);

			$limit=30;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Center Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_master_centre = $this->app->load_model("master_centre");
			//$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$master_centre = $obj_model_master_centre->execute("SELECT",false,"","master_centre.status='Active' ".$whereCond."","master_centre.id desc limit ".$start.",".$limit."");

			foreach($master_centre as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$clientList[]=array(
					"id"=>$id,
					"name"=>$item['name'],
					"address"=>$item['address']
					);
			}
			$result=["centerList"=>$clientList];
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