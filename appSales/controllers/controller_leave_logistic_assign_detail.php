<?php
class _leave_logistic_assign_detail extends controller {
	function init() {
	}

	function onload() 
	{ 
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));

		$leaveLogisticAssignID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('leaveLogisticAssignID'));
		$leaveLogisticAssignID=$this->app->utility->decrypt($leaveLogisticAssignID);

		$assignDate=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('assignDate'));
		$selectClientIDs=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('selectClientIDs'));
		$selectEmployeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('selectEmployeeID'));
		$selectEmployeeID=$this->app->utility->decrypt($selectEmployeeID);

		$leaveID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('leaveID'));
		$leaveID=$this->app->utility->decrypt($leaveID);
		
	
		if($employeeID!='' && $deviceType!='' && $action=='detail' && $leaveLogisticAssignID!='')
		{
			$editBtn='No';
			$deleteBtn='No';

			$obj_model_task = $this->app->load_model("employee_leave_assign_client");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_task->join_table(["employee"=>"employeeM"], "left", array(), array("assign_by_employee_id"=>"id"));
			$obj_model_task->join_table("employee_leave", "left", array(), array("employee_leave_id"=>"id"));
			$obj_model_task->join_table(["employee_leave"=>"employee_leave","employee"=>"employeeL"], "left", array(), array("employee_id"=>"id"));
			$employeeLeaveAssignClientData = $obj_model_task->execute("SELECT",false,"","employee_leave_assign_client.id='".$leaveLogisticAssignID."'");
			$leaveAssignDetail=$employeeLeaveAssignClientData[0];

			$headings[]=array("label"=>"id","value"=>'#'.$leaveAssignDetail['id']);
			$headings[]=array("label"=>"Assign Date","value"=>date('d-m-Y', strtotime($leaveAssignDetail['created_at'])));
			$headings[]=array("label"=>"Total Temporary Client","value"=>count(explode(',',$leaveAssignDetail['client_ids'])));

			$employeeImage=$this->app->utility->get_image_url($leaveAssignDetail["employee_image"],'employee','large');
			$employeeDetail[]=array(
				"id"=>$this->app->utility->encrypt($leaveAssignDetail['employee_id']),
				"heading"=>"Temporary Logistic Assign Details",
				"image"=>$employeeImage,
				"name"=>$leaveAssignDetail['employee_name'],
				"code"=>$leaveAssignDetail['employee_lms_employee_code'],
				"mobile"=>$leaveAssignDetail['employee_mobile'],
			);

			$employeeDetail[]=array(
				"id"=>$this->app->utility->encrypt($leaveAssignDetail['employeeM_id']),
				"heading"=>"Logistic Manager Details",
				"image"=>$employeeImage,
				"name"=>$leaveAssignDetail['employeeM_name'],
				"code"=>$leaveAssignDetail['employeeM_lms_employee_code'],
				"mobile"=>$leaveAssignDetail['employeeM_mobile'],
			);

			//make this always last, use for edit
			$employeeDetail[]=array(
				"id"=>$this->app->utility->encrypt($leaveAssignDetail['employeeL_id']),
				"heading"=>"Logistic Person Who is on Leave",
				"image"=>$employeeImage,
				"name"=>$leaveAssignDetail['employeeL_name'],
				"code"=>$leaveAssignDetail['employeeL_lms_employee_code'],
				"mobile"=>$leaveAssignDetail['employeeL_mobile'],
			);

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area","sample_pickup","sample_pickup_frequency"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","FIND_IN_SET(`client`.`id`,'".$leaveAssignDetail['client_ids']."')");

			foreach($client as $item)
			{
				$address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'client','large');
				$AssignClientList[]=array(
				"id"=>$id,
				"image"=>$image,
				"name"=>$item['company_name'],
				"address"=>$address!=''?$address:'',
				"mobile"=>$item['mobile'],
				"frequency"=>$item['client_detail_sample_pickup_frequency'],
				"days"=>$item['client_detail_sample_pickup']
				);
			}
			$today_date= date('Y-m-d H:i:s');
			if($leaveAssignDetail['assign_date']>$today_date && $employeeID==$leaveAssignDetail['assign_by_employee_id']){
				$editBtn='Yes';
				$deleteBtn='Yes';
			}

			$client_ids=[];
			$leaveAssignDetailArray=explode(',',$leaveAssignDetail['client_ids']);
			foreach($leaveAssignDetailArray as $item)
			{
				if($item!=''){
				array_push($client_ids,$this->app->utility->encrypt($item));
				}
			}

			$editDetail=["leaveID"=>$this->app->utility->encrypt($leaveAssignDetail['employee_leave_id']),"leaveEmployeeID"=>$this->app->utility->encrypt($leaveAssignDetail['employee_leave_employee_id']),"selectEmployeeID"=>$this->app->utility->encrypt($leaveAssignDetail['employee_id']),"selectClientIDs"=>implode(',',$client_ids),'assignDate'=>date('d-m-Y', strtotime($leaveAssignDetail['assign_date']))];

			$result=["headings"=>$headings,"employeeDetail"=>$employeeDetail,"AssignClientList"=>$AssignClientList,"editDetail"=>$editDetail,"editBtn"=>$editBtn,"deleteBtn"=>$deleteBtn];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($employeeID!='' && $deviceType!='' && $action=='addedit' && $selectEmployeeID!='' && $selectClientIDs!='' && $assignDate!='' && $leaveID!='')
		{
			$assignDate=date('Y-m-d', strtotime($assignDate));

			$client_ids=[];
			$selectedEmployee=explode(',',$selectClientIDs);
			foreach($selectedEmployee as $item)
			{
				if($this->app->utility->decrypt($item)!=0){
				array_push($client_ids,$this->app->utility->decrypt($item));
				}
			}

			$data_t=array();
			$data_t['employee_leave_id']=$leaveID;
			$data_t['assign_date']=$assignDate;
			$data_t['employee_id']=$selectEmployeeID;
			$data_t['client_ids']=implode(',',$client_ids);
			$data_t['assign_by_employee_id']=$employeeID;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model=$this->app->load_model("employee_leave_assign_client");
			$obj_model->map_fields($data_t);
			if($leaveLogisticAssignID=='') {
				$leaveLogisticAssignID=$obj_model->execute("INSERT");
				
				//send push start
				$data['employee_ids']=$selectEmployeeID;
				$title='Temporary Client Assign for '.date('d-m-Y', strtotime($assignDate));
				$message=count($selectedEmployee).' Client Assign.';
				$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'PickUpClientListActivity');
				$this->app->utility->send_push($data);

				//notification data insert
				$data_t=array();
				$data_t['noti_type']='Temporary Client Assign';
				$data_t['title']=$title;
				$data_t['description']=$message;
				$data_t['employee_ids']=$data['employee_ids'];
				$data_t['table_id']=$leaveLogisticAssignID;
				$data_t['created_by']=$employeeID;
				$data_t['created_at']=date("Y-m-d H:i:s");
				$obj_model_employee_task_master=$this->app->load_model("notifications");
				$obj_model_employee_task_master->map_fields($data_t);
				$obj_model_employee_task_master->execute("INSERT");
				//send push end

			}else{
				$obj_model->execute("UPDATE",false,"","id='".$leaveLogisticAssignID."'");	
			}
			
			$result=["leaveLogisticAssignID"=>$this->app->utility->encrypt($leaveLogisticAssignID)];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($employeeID!='' && $deviceType!='' && $action=='delete' && $leaveLogisticAssignID!='')
		{
			$data_t=array();
			$data_t['status']='Trash';
			$obj_model=$this->app->load_model("employee_leave_assign_client");
			$obj_model->map_fields($data_t);
			$obj_model->execute("UPDATE",false,"","id='".$leaveLogisticAssignID."'");	
			$message=array("message"=>"Deleted Successfully.","msgCode"=>"1");
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