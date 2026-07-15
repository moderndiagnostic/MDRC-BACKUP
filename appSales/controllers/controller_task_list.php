<?
class _task_create extends controller {
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
		
		$taskID='MQ==';

		$whereCond='';
		if($employeeID!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (employee.name LIKE '%$search%' or employee.phone LIKE '%$search%')";
			}

			$taskSelectedEmployee=["MQ==","Mg=="];

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
			$employee = $obj_model_employee->execute("SELECT",false,"","status='Active' ".$whereCond."","employee.id desc limit ".$start.",".$limit."");

			foreach($employee as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$selected='No';
				$employeeList[]=array("id"=>$id,"name"=>$item['name'],"image"=>'',"designation"=>'designation',"city"=>'city');
			}
			$result=["employeeList"=>$employeeList,"taskID"=>$taskID,"taskSelectedEmployee"=>$taskSelectedEmployee];
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