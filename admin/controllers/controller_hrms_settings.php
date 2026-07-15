<?
	class _hrms_settings extends controller{
		
		function init(){
			//$this->app->enable_cache("home.html");
		}
		
		function onload(){
			$this->assign("manage_for", "HRMS Settings");
			$this->assign("to_do", "");
		}
	}	
?>