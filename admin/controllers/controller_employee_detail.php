<?php
class _employee_detail extends controller
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
			$this->app->redirect("index.php?view=employee_list");
		}

		$obj_model_employee = $this->app->load_model("employee");
		$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
		$obj_model_employee->join_table("city", "left", array(), array("city_id"=>"id"));
		$employeeDetail = $obj_model_employee->execute("SELECT",false,"","employee.id='".$id."'"); 
		if(count($employeeDetail)>0)
		{
			$employee=$employeeDetail[0];
			$this->app->assign("employee", $employee);

			if($employee['reporting_employee_lms_id']!=0){
				$obj_model_employee = $this->app->load_model("employee");
				$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
				$obj_model_employee->join_table("city", "left", array(), array("city_id"=>"id"));
				$reportingDetail = $obj_model_employee->execute("SELECT",false,"","employee.lms_employee_id='".$employee['reporting_employee_lms_id']."'");
				$this->app->assign("reportingDetail", $reportingDetail[0]);
			} else {
				$this->app->assign("reportingDetail", []);
			}
			
		} 
		else 
		{
			$this->app->redirect("index.php?view=employee_list");
		}
	}	
}	
?>