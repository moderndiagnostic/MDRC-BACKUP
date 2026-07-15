<?php
class _task_office_list extends controller
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
	}

	function downloadOfficeTask()
	{
		$start_date=$this->app->getGetVar('start_date');
		$end_date=$this->app->getGetVar('end_date');
		
		$searchQuery='';
		if($start_date!='')
		{
			$searchQuery=" AND date(employee_task_office.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
		}
		$this->app->no_html=true;
		$obj_excel = $this->app->load_module("PHPExcel");
		$ExeclHeads=array("ID","Employee Name","Check IN","Check Out","Task Remark","Status","Latitude","Longitude","Device Type","Total Time");
		
		$obj_table = $this->app->load_model("employee_task_office");
		$obj_table->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
		$result = $obj_table->execute("SELECT", false, "", "employee_task_office.status!='Trash' ".$searchQuery." ","employee_task_office.id desc");
		
		$ucount=1;
		
		foreach($result as $user)
		{
			$check_in=date("d-m-Y H:i:s", strtotime($user['check_in']));
			$check_out=date("d-m-Y H:i:s", strtotime($user['check_out']));
			$diff=$this->app->utility->getTimeDiff(["startTime"=>$user['check_in'],"endTime"=>$user['check_out']]);

			$data_array[]=array("ID"=>$user['id'],"Employee Name"=>$user['employee_name'],"Check IN"=>$check_in,
			"Check Out"=>$check_out,"Task Remark"=>$user['task_remark'],"Status"=>$user['status'],
			"Latitude"=>$user['latitude'],"Longitude"=>$user['longitude'],"Device Type"=>$user['device_type'],"Total Time"=>$diff);
			$ucount++;
		}
		$filename="TaskofficeList-".date('d-m-Y');
		$this->app->utility->export_excel($ExeclHeads,$data_array,$ExeclHeads,$filename,$ExeclHeads);
	}	
}	
?>