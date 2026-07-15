<?php
class _employee_sample_pickup_list extends controller
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
		###
	}	
		
	function load_data()
	{
		###
	}

	function downloadSamplePickup()
	{
		
		$start_date=$this->app->getGetVar('start_date');
		$end_date=$this->app->getGetVar('end_date');

		$dateDiff=$this->app->utility->DateDiff(strtotime($end_date),strtotime($start_date));
		if($dateDiff>45){
			$this->app->redirect("index.php?view=employee_sample_pickup_list");
		}
		else
		{
			$searchQuery='';
			if($start_date!='')
			{
				$searchQuery=" AND date(employee_sample_pickup.created_at) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
			}
			$this->app->no_html=true;
			$obj_excel=$this->app->load_module("PHPExcel");
			
			$ExeclHeads=array(
				"Pickup Date",
				"Pickup Time",
				"Check IN",
				"Employee Code",
				"Employee Name",
				"Designation",
				"Client Name",
				"Client Mob No",
				"Latitude",
				"Longitude",
				"Total Distance",
				"Summary",
				"Status",
				// "Employee Email",
				// "Employee Mobile",
				// "Client Email",
				// "ID",
			);
	
			$obj_table = $this->app->load_model("employee_sample_pickup");
			$obj_table->join_table("client", "left", array("company_name","email","mobile"), array("client_id"=>"id"));
			$obj_table->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
			$obj_table->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
			$result = $obj_table->execute("SELECT", false, "", "employee_sample_pickup.id='117282' and employee_sample_pickup.status!='Trash' ".$searchQuery."","employee_sample_pickup.id desc");

			$ucount=1;
			
			foreach($result as $user)
			{

				$obj_table = $this->app->load_model("employee_sample_pickup_update");
				$task_update = $obj_table->execute("SELECT", false, "", "employee_sample_pickup_id='".$user['id']."'","id ASC");
	
				foreach($task_update as $taskUpdate){
					if($taskUpdate['pickup_status']=='Start Journey') {
						$latitude=$taskUpdate['latitude'];
						$longitude=$taskUpdate['longitude'];
						$check_in=$taskUpdate['updated_at']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['updated_at'])):"";
						$check_in1=$taskUpdate['updated_at'];
					}
					if($taskUpdate['pickup_status']=='Check In') {
						$googleAddress=$taskUpdate['google_address'];
						$check_in=$taskUpdate['updated_at']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['updated_at'])):"";
						$check_out=$taskUpdate['updated_at']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['updated_at'])):"";
						$latitude=$taskUpdate['latitude'];
						$longitude=$taskUpdate['longitude'];
					}
					if($taskUpdate['pickup_status']=='Check Out') {
						$latitude=$taskUpdate['latitude'];
						$longitude=$taskUpdate['longitude'];
						$check_out=$taskUpdate['updated_at']!=''?date("d-m-Y h:i a", strtotime($taskUpdate['updated_at'])):"";
						$check_out1=$taskUpdate['updated_at'];
					}
				}


				$diff=$this->app->utility->getTimeDiff(["startTime"=>$check_in,"endTime"=>$check_out]);

				$summary='Collect Sample : <b>'.$user['collect_sample'].'</b>';
				$summary.='<br/>Collect Payment : <b>'.$user['collect_payment'].'</b>';

				$data_array[]=array(
					"Pickup Date"=>$user['pickup_date'],
					"Pickup Time"=>$diff,
					"Check IN"=>$check_in,
					"Employee Code"=>$user['employee_lms_employee_code'],
					"Employee Name"=>$user['employee_name'],
					"Designation"=>$user['master_designation_name'],
					"Client Name"=>$user['client_company_name'],
					"Client Mob No"=>$user['client_mobile'],
					"Client Address"=>$googleAddress,
					"Latitude"=>$latitude,
					"Longitude"=>$longitude,
					"Total Distance"=>$user['distance_km'],
					"Summary"=>$summary,
					"Status"=>$user['status'],
					// "Employee Email"=>$user['employee_email'],
					// "Employee Mobile"=>$user['employee_mobile'],
					// "Client Email"=>$user['client_email'],
					// "ID"=>$user['id'],
				);
				
				$ucount++;
			}
			
			$filename="Employee-Sample-Pickup-List-".date('d-m-Y');
			$this->app->utility->export_excel($ExeclHeads,$data_array,$ExeclHeads,$filename,$ExeclHeads);
		}
		
	}
}	
?>