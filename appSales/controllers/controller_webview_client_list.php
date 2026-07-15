<?
class _webview_client_list extends controller {
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getGetVar('employeeID'));
		$selectEmployeeId=empty($this->app->getGetVar('selectEmployeeId'))?$employeeID:$this->app->getGetVar('selectEmployeeId');
		
		$employeeID=$this->app->utility->decrypt($employeeID);
		$selectEmployeeId=$this->app->utility->decrypt($selectEmployeeId);
		
		if($selectEmployeeId!='')
		{
			$obj_model_employee = $this->app->load_model("employee");
			$employee = $obj_model_employee->execute("SELECT",false,"","id='".$selectEmployeeId."'");

			if(count($employee)>0)
			{
				if($employee[0]['status']!='Active')
				{
					$this->app->redirect("index.php?view=webview_blank&webview=close");
					exit;
				}

				$employeeData=$this->app->utility->getTopEmployee($employee[0]['reporting_employee_lms_id']);
				if(count($employeeData)==1 && $employee[0]['id']==$employeeData[0]['id']){
					$this->app->assign("topEmployee", []);
					
				}else{
					$this->app->assign("topEmployee",array_reverse($employeeData));
				}
				//employee details
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