<?
class _webview_my_team extends controller {
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getGetVar('employeeID'));
		$selectEmployeeId=empty($this->app->getGetVar('selectEmployeeId'))?$employeeID:$this->app->getGetVar('selectEmployeeId');
		
		$employeeID=$this->app->utility->decrypt($employeeID);
		$selectEmployeeId=$this->app->utility->decrypt($selectEmployeeId);

		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getGetVar('employeePhone'));
		
		if($employeeID!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$obj_model_employee->join_table("master_designation", "left", array("name"), array("master_designation_id"=>"id"));
			$employee = $obj_model_employee->execute("SELECT",false,"","employee.id='".$selectEmployeeId."'");

			if(count($employee)>0)
			{
				if($employee[0]['status']!='Active')
				{
					$this->app->redirect("index.php?view=webview_blank&webview=close");
					exit;
				}
				//employee details

				$employeeData=$this->app->utility->getTopEmployee($employee[0]['reporting_employee_lms_id']);
				if(count($employeeData)==1 && $employee[0]['id']==$employeeData[0]['id']){
					$this->app->assign("topEmployee", []);
					
				}else{
					$this->app->assign("topEmployee",array_reverse($employeeData));
				}
				$this->app->assign("employee", $employee[0]);
			}
			else
			{
				$this->app->redirect("index.php?view=webview_blank&webview=close");
				exit;
			}
		}
		else
		{
			$this->app->redirect("index.php?view=webview_blank&webview=close");
			exit;
		}
	}
}
?>