<?php
class _client_logistic_assign_detail extends controller
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
			$this->app->redirect("index.php?view=client_logistic_assign_list");
		}
		$obj_model_client_logistic_assign = $this->app->load_model("client_logistic_assign");
		$obj_model_client_logistic_assign->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"employee"], "left", array(), array("employee_id"=>"id"));
		$obj_model_client_logistic_assign->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"Employee"], "left", array(), array("logistic_manager_employee_id"=>"id"));
		$obj_model_client_logistic_assign->join_table(["client_logistic_assign"=>"client_logistic_assign","employee"=>"EMPLOYEE"], "left", array(), array("assign_by_employee_id"=>"id"));
		$client_logistic_assign = $obj_model_client_logistic_assign->execute("SELECT",false,"","client_logistic_assign.id='".$id."'");
		
		if(count($client_logistic_assign)>0)
		{
			$status=$this->app->utility->get_client_logistic_assign_status(["status"=>$client_logistic_assign[0]["request_status"]]);
			//get client detail
			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array(), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.id='".$client_logistic_assign[0]['client_id']."'","client.id desc limit 0,1");

			$client_logistic_assign_history = $this->app->load_model("client_logistic_assign_history");
			$client_logistic_history = $client_logistic_assign_history->execute("SELECT",false,"","id='".$id."'");

			$this->app->assign("client_logistic_history", $client_logistic_history);
			$this->app->assign("client_logistic_assign", $client_logistic_assign[0]);
			$this->app->assign("client", $client[0]);
			$this->app->assign("badge", $status['badge']);
		}
		else
		{
			$this->app->redirect("index.php?view=client_logistic_assign_list");
		}
	}	
}	
?>