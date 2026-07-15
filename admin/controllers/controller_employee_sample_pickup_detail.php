<?php
class _employee_sample_pickup_detail extends controller
{
	function init()
	{
		if($this->app->getCurrentAction()=="")
		{
			$this->load_data();
		}
	}

	function onload()
	{
	}	
		
	function load_data()
	{
		$id=$this->app->getGetVar('id');
		if($id=='')
		{
			$this->app->redirect("index.php?view=employee_sample_pickup_list");
		}

		$obj_model_employee_sample_pickup = $this->app->load_model("employee_sample_pickup");
		$employee_sample_pickup = $obj_model_employee_sample_pickup->execute("SELECT",false,"","employee_sample_pickup.id='".$id."'");
		if(count($employee_sample_pickup)>0)
		{
			//get all details
			$status=$this->app->utility->get_employee_sample_pickup_status(["status"=>$employee_sample_pickup[0]["status"]]);

			//get client detail
			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array(), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.id='".$employee_sample_pickup[0]['client_id']."'","client.id desc limit 0,1");

			//get sales person tagged if client is from lis
			if($client[0]['lms_employee_id']>0) {
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$salesPerson = $obj_model_employee->execute("SELECT",false,"","lms_employee_id='".$client[0]['lms_employee_id']."'","employee.id desc limit 0,1");
			} else {
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$salesPerson = $obj_model_employee->execute("SELECT",false,"","employee.id='".$client[0]['client_detail_added_by_employee_id']."'","employee.id desc limit 0,1");
			}
			$this->app->assign("salesPerson", $salesPerson[0]);

			//get logistic person details
			$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
			$logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_id='".$client[0]['id']."'","id desc limit 0,1");
			if(count($logistic_assign)>0) {
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
				$logistic = $obj_model_employee->execute("SELECT",false,"","employee.id='".$logistic_assign[0]['employee_id']."'","employee.id desc limit 0,1");
			} else {
				$logistic=[];
			}
			$this->app->assign("logistic", $logistic[0]);

			//get lab details
			if($client[0]['client_detail_invoice_to_center']>0) {
				//get sales person details
				$obj_model_lab = $this->app->load_model("master_centre");
				$labDetail = $obj_model_lab->execute("SELECT",false,"","lms_center_id='".$client[0]['client_detail_invoice_to_center']."'","master_centre.id desc limit 0,1");
			} else {
				$labDetail=[];
			}
			$this->app->assign("labDetail", $labDetail[0]);

			//get sample_pickup_update
			$obj_model_sample_pickup_update = $this->app->load_model("employee_sample_pickup_update");
			$sample_pickup_update = $obj_model_sample_pickup_update->execute("SELECT",false,"","employee_sample_pickup_id='".$employee_sample_pickup[0]['id']."'");
			$this->app->assign("sample_pickup_update", $sample_pickup_update);
			$collectAmount=0;
			foreach($sample_pickup_update as $item) {
				if($item['pickup_status']=='Payment' && $item['collect_payment_amount']>0 && $item['collect_payment_otp_verify']=='Yes') {
					$collectAmount=$item['collect_payment_amount'];
				}	
			}
			$this->app->assign("collectAmount", $collectAmount);


			//get sample pickup images
			$obj_model_pickup_images = $this->app->load_model("employee_sample_pickup_images");
			$pickup_images = $obj_model_pickup_images->execute("SELECT",false,"","employee_sample_pickup_id='".$employee_sample_pickup[0]['id']."'");
			$this->app->assign("pickup_images", $pickup_images);


			$this->app->assign("sample_pickup", $employee_sample_pickup[0]);
			$this->app->assign("client", $client[0]);
			$this->app->assign("badge", $status['badge']);
		}
		else
		{
			$this->app->redirect("index.php?view=employee_sample_pickup_list");
		}
	}	
}	
?>