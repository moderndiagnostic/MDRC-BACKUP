<?
class _sample_dispatch_addedit extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('action'));

		$sampleDispatchId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("sampleDispatchId"));
		$sampleDispatchId=$this->app->utility->decrypt($sampleDispatchId);
		
		if($employeeID=='' || $action=='') {
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($action=='addedit')
		{
			$centerId=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("centerId"));
			$centerId=$this->app->utility->decrypt($centerId);

			$courierType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("courierType"));
			$courierName=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("courierName"));
			$courierPerson=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("courierPerson"));
			$courierMobile=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("courierMobile"));
			$deliveryDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deliveryDate"));
			$deliveryTime=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deliveryTime"));
			$sampleCount=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("sampleCount"));
			
			if($centerId=='') {
				$message=array("message"=>"Please Select Center.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			if($sampleDispatchId!='') {
				$obj_model_employee_sample_dispatch_check = $this->app->load_model("employee_sample_dispatch");
				$employee_sample_dispatch = $obj_model_employee_sample_dispatch_check->execute("SELECT",false,"","id='".$sampleDispatchId."'","id desc limit 0,1");
				if(count($employee_sample_dispatch)<=0) {
					$message=array("message"=>"Something Gone Wrong.","msgCode"=>"0");
					$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
					echo $this->app->utility->indent($opt); exit;
				}
			}

			$data_t=array();
			$data_t['courier_type']=$courierType;
			$data_t['courier_name']=$courierName;
			$data_t['courier_person']=$courierPerson;
			$data_t['courier_mobile']=$courierMobile;
			$data_t['courier_delivery_date']=$deliveryDate;
			$data_t['sample_count']=$sampleCount;
			$data_t['sent_center_id']=$centerId;
			$data_t['status']='Dispatched';
			$data_t['created_at']=date("Y-m-d H:i:s");
			$data_t['employee_id']=$employeeID;
			$data_t['receive_created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("employee_sample_dispatch");
			$obj_model_client->map_fields($data_t);
			if($sampleDispatchId=='') {
				$employee_sample_dispatch_id=$obj_model_client->execute("INSERT",false,"","");
			}else {
				$obj_model_client->execute("UPDATE",false,"","id='".$sampleDispatchId."'");
				$employee_sample_dispatch_id=$sampleDispatchId;
			}

			$data_t=array();
			$data_t['employee_sample_dispatch_id']=$employee_sample_dispatch_id;
			$data_t['courier_delivery_time']=$deliveryTime;
			$data_t['updated_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("employee_sample_dispatch_other_detail");
			$obj_model_client->map_fields($data_t);
			if($sampleDispatchId=='') {
				$obj_model_client->execute("INSERT",false,"","");

				//send push start
				$obj_model_client=$this->app->load_model("master_centre");
				$masterCentre=$obj_model_client->execute("SELECT",false,"","id='".$centerId."'");
				if($masterCentre[0]['employee_id']>0)
				{
					$obj_model_employee=$this->app->load_model("employee");
					$employeeDetail=$obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'");
					
					$data['employee_ids']=$masterCentre[0]['employee_id'];
					$title='Sample Dispatch by '.$employeeDetail[0]['name'].' on '.$courierName;
					$message='Will be Delivered on '.$deliveryDate.'.';
					$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'NotificationListActivity');
					$this->app->utility->send_push($data);

					//notification data insert
					$data_t=array();
					$data_t['noti_type']='Sample Dispatch Detail';
					$data_t['title']=$title;
					$data_t['description']=$message;
					$data_t['employee_ids']=$data['employee_ids'];
					$data_t['table_id']=$employee_sample_dispatch_id;
					$data_t['created_by']=$employeeID;
					$data_t['created_at']=date("Y-m-d H:i:s");
					$obj_model_employee_task_master=$this->app->load_model("notifications");
					$obj_model_employee_task_master->map_fields($data_t);
					$obj_model_employee_task_master->execute("INSERT");
				}
				//send push end

			}else {
				$obj_model_client->execute("UPDATE",false,"","employee_sample_dispatch_id='".$sampleDispatchId."'");
			}
			$message='Dispatch Data Saved.';

			$result=["sampleDispatchId"=>$this->app->utility->encrypt($employee_sample_dispatch_id)];
			$message=array("message"=>$message,"msgCode"=>"1","result"=>$result);
		}
		else if($action=='delete')
		{
			if($sampleDispatchId=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$data_t=array();
			$data_t['status']='Trash';
			$obj_model_client=$this->app->load_model("employee_sample_dispatch");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$sampleDispatchId."'");
			
			$message=array("message"=>"Data Removed.","msgCode"=>"1");
		}
		else if($action=='accept')
		{
			if($sampleDispatchId=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model=$this->app->load_model("employee_sample_dispatch");
			$employeeSampleDispatch=$obj_model->execute("SELECT",false,"","id='".$sampleDispatchId."'");

			$data_t=array();
			$data_t['status']='Delivered';
			$data_t['receive_employee_id']=$employeeID;
			$data_t['receive_created_at']=date("Y-m-d H:i:s");
			$obj_model_client=$this->app->load_model("employee_sample_dispatch");
			$obj_model_client->map_fields($data_t);
			$obj_model_client->execute("UPDATE",false,"","id='".$sampleDispatchId."'");

			//send push start
			$obj_model_employee=$this->app->load_model("employee");
			$employeeDetail=$obj_model_employee->execute("SELECT",false,"","id='".$employeeID."'");
			
			$data['employee_ids']=$employeeSampleDispatch[0]['employee_id'];
			$title='Sample Received by '.$employeeDetail[0]['name'];
			$message='on '.date("d-m-Y");
			$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'NotificationListActivity');
			$this->app->utility->send_push($data);

			//notification data insert
			$data_t=array();
			$data_t['noti_type']='Sample Dispatch Detail';
			$data_t['title']=$title;
			$data_t['description']=$message;
			$data_t['employee_ids']=$data['employee_ids'];
			$data_t['table_id']=$sampleDispatchId;
			$data_t['created_by']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_employee_task_master=$this->app->load_model("notifications");
			$obj_model_employee_task_master->map_fields($data_t);
			$obj_model_employee_task_master->execute("INSERT");
			//send push end
			
			$message=array("message"=>"Data Removed.","msgCode"=>"1");
		}
		else if($action=='photoUpload')
		{
			if($sampleDispatchId=='' || $employeeID=='') {
				$message=array("message"=>"Data Required.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$dir_name =ABS_PATH.'/uploads/sampleDispatch/'.$sampleDispatchId;
			if (!is_dir($dir_name)) {
				mkdir($dir_name, 0777, true);
			}

			if($_FILES['image1']['name']!='') {
				$image1=$this->app->utility->FileUpload(['filename'=>$_FILES['image1']['name'],'filetmpname'=>$_FILES['image1']['tmp_name'],'folder'=>"sampleDispatch/".$sampleDispatchId]);
			}

			if($_FILES['image2']['name']!='') {
				$image2=$this->app->utility->FileUpload(['filename'=>$_FILES['image2']['name'],'filetmpname'=>$_FILES['image2']['tmp_name'],'folder'=>"sampleDispatch/".$sampleDispatchId]);
			}

			$data_t=array();
			if($image1!=''){
				$data_t['package_photo']=$image1;
			}
			if($image2!=''){
				$data_t['receipt_photo']=$image2;
			}
			$obj_model_sample=$this->app->load_model("employee_sample_dispatch_other_detail");
			$obj_model_sample->map_fields($data_t);
			$obj_model_sample->execute("UPDATE",false,"","id='".$sampleDispatchId."'");

			$message=array("message"=>"Updated.","msgCode"=>"1");
		}
		else if($action=='getSampleDispatchData')
		{
			$obj_model_employee_sample_dispatch_check = $this->app->load_model("employee_sample_dispatch");
			$obj_model_employee_sample_dispatch_check->join_table("employee_sample_dispatch_other_detail", "left", array(), array("id"=>"employee_sample_dispatch_id"));
			$employee_sample_dispatch = $obj_model_employee_sample_dispatch_check->execute("SELECT",false,"","employee_sample_dispatch.id='".$sampleDispatchId."'");
			if(count($employee_sample_dispatch)<=0) {
				$message=array("message"=>"Something Gone Wrong.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			} 
			$item=$employee_sample_dispatch[0];
			$sampleDispatchDetail=[
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
			$result=["sampleDispatchDetail"=>$sampleDispatchDetail];
			$message=array("message"=>"Detail","msgCode"=>"1","result"=>$result);
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