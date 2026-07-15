<?
class _sample_dispatch_detail extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$sampleDispatchId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('sampleDispatchId'));
		$sampleDispatchId=$sampleDispatchId!=''?$this->app->utility->decrypt($sampleDispatchId):'';

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));
		
		if($employeeID!='' && $sampleDispatchId!='')
		{
			$obj_model = $this->app->load_model("employee_sample_dispatch");
			$obj_model->join_table("employee_sample_dispatch_other_detail", "left", array(), array("id"=>"employee_sample_dispatch_id"));
			$obj_model->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model->join_table("master_centre", "left", array(), array("sent_center_id"=>"id"));
			$sample_dispatch = $obj_model->execute("SELECT",false,"","employee_sample_dispatch.id='".$sampleDispatchId."'");
			$item=$sample_dispatch[0];
			
			$pickupDetail[]=["title"=>"Package ID ","value"=>'#'.$item['id']];
			$pickupDetail[]=["title"=>"Status","value"=>$item['status']];
			$pickupDetail[]=["title"=>"Dispatch Date","value"=>date('d-m-Y h:i A', strtotime($item['created_at']))];
			$pickupDetail[]=["title"=>"Total Sample","value"=>$item['sample_count']];
			
			$dispatchDetail=$this->getDispatchDetail($item,$employeeID);
			$pickupButtons=$this->getButtonStatus($item,$employeeID,$action);
			$personAssigned=$this->getpersonAssigned($item,$employeeID);

			$result=[
				"samplePickupID"=>$this->app->utility->encrypt($sampleDispatchId),
				"pickupDetail"=>$pickupDetail,
				"dispatchDetail"=>$dispatchDetail,
				"pickupButtons"=>$pickupButtons['buttons'],
				"summary"=>$pickupButtons['summary'],
				"personList"=>$personAssigned];
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

	function getDispatchDetail($item,$employeeID)
	{
		$sampleDispatchId=$item['id'];
		$dispatchDetail=[
			"id"=>$this->app->utility->encrypt($item['id']),
			"courierType"=>$item['courier_type'],
			"courierName"=>$item['courier_name'],
			"courierPerson"=>$item['courier_person'],
			"courierMobile"=>$item['courier_mobile'],
			"courierDeliveryDate"=>$item['courier_delivery_date'],
			"sampleCount"=>$item['sample_count'],
			"courierDeliveryTime"=>$item['employee_sample_dispatch_other_detail_courier_delivery_time'],
			"packagePhoto"=>$item['employee_sample_dispatch_other_detail_package_photo']!=''?SERVER_ROOT.'/uploads/sampleDispatch/'.$sampleDispatchId.'/'.$item['employee_sample_dispatch_other_detail_package_photo']:"",
			"receiptPhoto"=>$item['employee_sample_dispatch_other_detail_receipt_photo']!=''?SERVER_ROOT.'/uploads/sampleDispatch/'.$sampleDispatchId.'/'.$item['employee_sample_dispatch_other_detail_receipt_photo']:"",
			"status"=>$item['status'],
		];
		return $dispatchDetail;	
	}

	function getButtonStatus($item,$employeeID,$action) {

		$statusUpdate[]=[
			"date"=>date('d-m-Y h:i A', strtotime($item['created_at'])),
			"title"=>'Dispatched',
			"detail"=>'',
			"latitude"=>'',
			"longitude"=>'',
			"distance"=>''
		];

		if($item['status']=='Delivered')
		{
			$statusUpdate[]=[
				"date"=>date('d-m-Y h:i A', strtotime($item['created_at'])),
				"title"=>'Delivered',
				"detail"=>'',
				"latitude"=>'',
				"longitude"=>'',
				"distance"=>''
			];
		}

		$edit='No';
		$delete='No';
		$accept='No';

		if($action=='Receive')
		{
			if($item['status']!='Delivered')
			{
				$accept='Yes';
			}
		}
		else
		{
			if($item['status']=='Dispatched' && $item['employee_id']==$employeeID)
			{
				$edit='Yes';
				$delete='Yes';
			}
		}

		$buttons=[
			"edit"=>$edit,
			"delete"=>$delete,
			"accept"=>$accept,
		];

		return ["buttons"=>$buttons,"summary"=>$statusUpdate];
	}

	function getpersonAssigned($item,$employeeID) {
		
		$personList=[];
		//get sales person details
		$obj_model_employee = $this->app->load_model("employee");
		$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
		$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$item['employee_id ']."'","employee.id desc limit 0,1");
		if(count($employeeDetail)>0){
			$employee=$employeeDetail[0];
			$image=$this->app->utility->get_image_url($employee["image"],'employee','large');
			$personList[]=[
				"id"=>$this->app->utility->encrypt($employee['id']),
				"heading"=>'Sent From',
				"name"=>$employee['name'],
				"image"=>$image,
				"detail"=>$employee['master_designation_name'],
				"mobile"=>$employee['mobile']
			];
		}
		
		$obj_model_master_center = $this->app->load_model("master_centre");
		$employeeDetail = $obj_model_master_center->execute("SELECT",false,"","master_centre.id='".$item['sent_center_id']."'","master_centre.id desc limit 0,1");
		if(count($employeeDetail)>0){
			$employee=$employeeDetail[0];
			$personList[]=[
				"id"=>$this->app->utility->encrypt($employee['id']),
				"heading"=>'Center',
				"name"=>$employee['name'],
				"image"=>'',
				"detail"=>$employee['address'],
				"mobile"=>$employee['mobile']
			];
		}
		
		return $personList;
	}
}
?>