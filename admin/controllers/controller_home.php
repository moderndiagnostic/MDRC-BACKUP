<?php
class _home extends controller
{
	function init() {
	}
	
	function onload()
	{
		/* $obj_table_tble= $this->app->load_model('service');
		$rs_tble= $obj_table_tble->execute("SELECT", false, "SELECT count(*) as total_projects from service where status='Active'");
		$this->assign("total_c", $rs_tble[0]['total_projects']);
		
		$obj_table_tble= $this->app->load_model('work');
		$rs_tble= $obj_table_tble->execute("SELECT", false, "SELECT count(*) as total_projects from work WHERE status='Active'");
		$this->assign("total_csr", $rs_tble[0]['total_projects']);
		
		$s_date=date('d-m-Y');
		$obj_table_tble= $this->app->load_model('contact_enquiry');
		$rs_tble= $obj_table_tble->execute("SELECT", false, "SELECT count(*) as total_projects from contact_enquiry WHERE STR_TO_DATE(`added_date`, '%d-%m-%Y') BETWEEN STR_TO_DATE('".$s_date."', '%d-%m-%Y') AND STR_TO_DATE('".$s_date."', '%d-%m-%Y')");
		$this->assign("total_inq", $rs_tble[0]['total_projects']); */
	}
}
