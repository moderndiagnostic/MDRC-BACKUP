<?php
class _task_detail extends controller
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
			$this->app->redirect("index.php?view=task_list");
		}

		$obj_model_employee_task_master = $this->app->load_model("employee_task_master");
		$task_master = $obj_model_employee_task_master->execute("SELECT",false,"","employee_task_master.id='".$id."'");
		if(count($task_master)>0) {

			$task=$task_master[0];
			$status=$this->app->utility->get_employee_sample_pickup_status(["status"=>$task["status"]]);
			$this->app->assign("badge", $status['badge']);
			$this->app->assign("task", $task);

			//get client detail
			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array(), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array(), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array(), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.id='".$task['client_id']."'","client.id desc limit 0,1");
			$this->app->assign("client", $client[0]);

			//get all employee
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","FIND_IN_SET(employee.id,'".$task['employee_ids']."')");
			$this->app->assign("employee", $employee);

			//get assignee employee
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$assigne = $obj_model_employee->execute("SELECT",false,"","employee.id='".$task['assign_by_employee_id']."'");
			$this->app->assign("assigne", $assigne[0]);

			//get update of task 
			$obj_model_employee = $this->app->load_model("employee_task_master_update");
			$update = $obj_model_employee->execute("SELECT",false,"","employee_task_master_id='".$task['id']."'");
			$this->app->assign("update", $update);
			foreach($update as $item) {
				if($item['activity']=='Check Out') {
					$finaUpdate=["meetDr"=>$item['meeting_client_meet'],"remark"=>$item['meeting_remark'],"meetingStatus"=>$item['meetingStatus']];
				}
			}
			$this->app->assign("finaUpdate", !empty($finaUpdate)?$finaUpdate:'');

		}else {
			$this->app->redirect("index.php?view=task_list");
		}
	}	
}	
?>