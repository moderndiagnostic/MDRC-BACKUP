<?php
class _client_detail extends controller
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
			$this->app->redirect("index.php?view=client_list");
		}

		$obj_model_client = $this->app->load_model("client");
		$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
		$client = $obj_model_client->execute("SELECT",false,"","client.id='".$id."'");
		if(count($client)>0)
		{
			$this->app->assign("client", $client[0]);
		}
		else
		{
			$this->app->redirect("index.php?view=client_list");
		}
	}	
}	
?>