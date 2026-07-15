<?
class _hub_employee_select_list extends controller {
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
		
		
		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$employeeID."'","employee.id desc limit 0,1");

			if($employee[0]['master_designation_name']!="Logistics Manager")
			{
				$message=array("message"=>"Please login as Logistics Manager.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt);
				exit;
			}

			$whereCond.=" and (employee.reporting_employee_lms_id='".$employee[0]['lms_employee_id']."')";

			if($search!='') {
				$whereCond.=" and (employee.name LIKE '%$search%' or employee.mobile LIKE '%$search%')";
			}

			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","status='Active' ".$whereCond."","employee.id desc");
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
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'employee','large');
				$employeeList[]=array("id"=>$id,"name"=>$item['name'],"image"=>$image,"designation"=>$item['master_designation_name'],"city"=>$item['city_name'],"mobile"=>$item['mobile']);
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