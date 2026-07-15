<?
class _leave_logistic_data extends controller {
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
		$status=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("status"));
		$loginAs=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("loginAs"));
		$leaveDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("leaveDate"));
		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));

		$selectedEmployeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('selectedEmployeeID'));
		$selectedEmployeeID=$this->app->utility->decrypt($selectedEmployeeID);

		if($page==1){
			$message=array("message"=>"No Data Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt);
			exit;
		}
		
		if($employeeID!='' && $deviceType!='' && $leaveDate!='' && $action=='logistic')
		{
			//check logistic manager
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$employeeID."'");

			if(count($employee)<=0 || $employee[0]['master_designation_name']!='Logistics Manager'){
				$message=array("message"=>"Login as Logistics Manager.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt);
				exit;
			}

			//find logistic employee of that logistic person
			$obj_model_employee = $this->app->load_model("employee");
			$logisticEmployee = $obj_model_employee->execute("SELECT",false,"","reporting_employee_lms_id ='".$employee[0]['lms_employee_id']."' and id!='".$selectedEmployeeID."'");
			if(count($employee)<=0){
				$message=array("message"=>"No Data Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt);
				exit;
			}
			
			foreach($logisticEmployee as $item)
			{
				$employeeImage=$this->app->utility->get_image_url($item["image"],'employee','large');

				$id=$this->app->utility->encrypt($item['id']);
				$employeeID=$this->app->utility->encrypt($item['employee_id']);

				$obj_model = $this->app->load_model("client_logistic_assign");
				$det = $obj_model->execute("SELECT",false,"SELECT count(id) as total from client_logistic_assign where employee_id='".$item['id']."' and request_status='Active'");

				$logisticEmployeeList[]=array(
				"id"=>$id,
				"image"=>$employeeImage,
				"name"=>$item['name'],
				"designation"=>$item['lms_employee_code'],
				"mobile"=>$item['mobile'],
				"total_client"=>$det[0]['total'],
				"temporary_client"=>0
				);
			}
			$result=["logisticEmployeeList"=>$logisticEmployeeList];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		} 
		else if($employeeID!='' && $deviceType!='' && $leaveDate!='' && $action=='client')
		{
			//check employee
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$selectedEmployeeID."'");

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area","sample_pickup","sample_pickup_frequency"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_logistic_assign", "left", array("employee_id"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' and client_logistic_assign.employee_id='".$employee[0]['id']."'","client.id desc");
			if(count($client)<=0) {
				$message=array("message"=>"No Client Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt);
				exit;
			}

			foreach($client as $item)
			{
				$address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'client','large');
				$logisticClientList[]=array(
				"id"=>$id,
				"image"=>$image,
				"name"=>$item['company_name'],
				"address"=>$address!=''?$address:'',
				"mobile"=>$item['mobile'],
				"frequency"=>$item['client_detail_sample_pickup_frequency'],
				"days"=>$item['client_detail_sample_pickup']
				);
			}
			$result=["logisticClientList"=>$logisticClientList];
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