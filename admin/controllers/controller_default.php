<?php
	class _default extends controller
	{
		function init()
		{
		}
		
		function onload()
		{
			//check if cookie is set or not
			if(isset($_COOKIE['MDRCSALES']))
			{
				 $obj_model_admin = $this->app->load_model("employee");
				 $rsUser = $obj_model_admin->execute("SELECT",false,"","(mobile='".$_COOKIE['MDRCSALES']."' or email='".$_COOKIE['MDRCSALES']."') and status='Active'");
				
				 if(count($rsUser)==1)
				 {
					$_SESSION['employeeId'] = $rsUser[0]['id'];
					$_SESSION['employeeName'] = $rsUser[0]['name'];
					$this->app->redirect("index.php?view=home");	
				 }
				 else
				 {
					unset($_SESSION['employeeId']);
					unset($_SESSION['employeeName']);
					unset($_COOKIE['MDRCSALES']);
					setcookie("MDRCSALES", '', time() - 3600, '/');
					$this->app->utility->set_message("You have successfully logged out of the system", "SUCCESS");
					$this->app->redirect("index.php");
				 }
			}
		}

	}	

?>