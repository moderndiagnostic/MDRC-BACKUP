<?php
class _employee_sample_dispatch_detail extends controller
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
			$this->app->redirect("index.php?view=employee_sample_dispatch_list");
		}

		$obj_model_employee_sample_pickup = $this->app->load_model("employee_sample_dispatch");
		$obj_model_employee_sample_pickup->join_table("employee_sample_dispatch_other_detail", "left", array(), array("id"=>"employee_sample_dispatch_id"));
		$obj_model_employee_sample_pickup->join_table("employee", "left", array("name","mobile"), array("employee_id"=>"id"));
		$obj_model_employee_sample_pickup->join_table(["employee"=>"employee_s"], "left", array("name"), array("receive_employee_id"=>"id"));
		$obj_model_employee_sample_pickup->join_table("master_centre", "left", array(), array("sent_center_id"=>"id"));
		$employee_sample_pickup = $obj_model_employee_sample_pickup->execute("SELECT",false,"","employee_sample_dispatch.id='".$id."'");
		if(count($employee_sample_pickup)>0)
		{
			//get all details
			$status=$this->app->utility->get_employee_sample_dispatch_status(["status"=>$employee_sample_pickup[0]["status"]]);

		
			
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