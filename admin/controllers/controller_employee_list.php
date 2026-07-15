<?php
class _employee_list extends controller
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
		$obj_model_employee=$this->app->load_model("master_designation");
		$designation=$obj_model_employee->execute("SELECT",false,"","status!='Trash'");
		$this->app->assign('designation',array_column($designation,'name','id'));
	}	
		
	function load_data()
	{
	}	
}	
?>