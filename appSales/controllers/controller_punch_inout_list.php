<?
class _punch_inout_list extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
	
		if($employeeID=='')
		{
			$message=array("message"=>"Data is missing.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$obj_model_employee = $this->app->load_model("employee");
		$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."' and employee.status!='Trash'","employee.id desc");
		if(count($employee)<=0) { 
			$message=array("message"=>"No Employee Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}
		
		$whereCond='employee_id="'.$employeeID.'"';
		$whereCond.=!empty($search)?' and punch_date="'.date('Y-m-d', strtotime($search)).'"':'';
		$obj_model_employee_punch_inout = $this->app->load_model("employee_punch_inout");
		$employee_punch_inout = $obj_model_employee_punch_inout->execute("SELECT",false,"",$whereCond,"id desc");
		$count=count($employee_punch_inout);

		$limit=10;
		$total_pages=intval($count/$limit);

		if($count<=0 || $total_pages<$page) {
			$message=array("message"=>"No Data Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$start=$page==0?0:($page)*$limit;

		$obj_model_employee_punch_inout = $this->app->load_model("employee_punch_inout");
		$punch_inout = $obj_model_employee_punch_inout->execute("SELECT",false,"",$whereCond,"employee_punch_inout.id desc limit ".$start.",".$limit."");

		foreach($punch_inout as $item)
		{
			$id=$this->app->utility->encrypt($item['id']);
			$image=$this->app->utility->get_image_url($item["employee_photo"],'punch','large');
			$punchInoutList[]=array(
				"id"=>$id,
				"number"=>'#'.$item['id'],
				"date"=>$item['punch_date'],
				"punchIn"=>date('h:i A', strtotime($item['punchin_datetime'])),
				"punchOut"=>$item['punchout_datetime']!=''?date('h:i A', strtotime($item['punchout_datetime'])):'',
				"image"=>$image,
				"heading"=>$item['pincode'],
				"subHeading"=>$item['map_address'],
				"latitude"=>$item['latitude'],
				"longitude"=>$item['longitude']
			);
		}
		$result=["punchInoutList"=>$punchInoutList];
		$message=array("message"=>"success","msgCode"=>"1","result"=>$result);

		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>