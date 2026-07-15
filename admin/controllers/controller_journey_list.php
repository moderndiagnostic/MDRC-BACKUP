<?php
class _journey_list extends controller
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

	function downloadJourney()
	{
		$start_date=$this->app->getGetVar('start_date');
		$end_date=$this->app->getGetVar('end_date');

		$dateDiff=$this->app->utility->DateDiff(strtotime($end_date),strtotime($start_date));
		if($dateDiff>45){
			$this->app->redirect("index.php?view=journey_list");
		}
		else
		{
			$searchQuery='';
			if($start_date!='')
			{
				$searchQuery=" AND date(employee_daily_journey.journey_date) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
			}
			$this->app->no_html=true;
			$obj_excel=$this->app->load_module("PHPExcel");
			$ExeclHeads=array(
				"ID",
				"Employee Name",
				"Employee Code",
				"Employee Mobile",
				"Date",
				"Start Time",
				"End Time",
				"Start KM",
				"End KM",
				"Total KM",
				"Total RS",
				"Status",
				"Manager Approved",
				"Manager Code",
				"Manager Approve Time",
				"Finance Manager Approved",
				"Finance Manager Code",
				"Finance Manager Approve Time",
			);
	
			$obj_table = $this->app->load_model("employee_daily_journey");
			$obj_table->join_table("employee", "left", array("name","lms_employee_code","mobile"), array("employee_id"=>"id"));
			$obj_table->join_table("employee_detail", "left", array(), array("employee_id"=>"employee_id"));
			$obj_table->join_table("employee_daily_journey_detail", "left", array(), array("id"=>"employee_daily_journey_id"));
			$result = $obj_table->execute("SELECT", false, "", "employee_daily_journey.id!='0' ".$searchQuery."","employee_daily_journey.id desc");
			$diff='';
			$ucount=1;
			
			foreach($result as $user)
			{
				# MANAGER
				$managerCode = '';
				$managerApproved = 'Pending';
				if($user['employee_daily_journey_detail_manager_datetime']!=''){
					$obj_emp = $this->app->load_model("employee");
					$emp = $obj_emp->execute("SELECT", false, "", "id='".$user['employee_daily_journey_detail_manager_employee_id']."'");
					$managerCode = $emp[0]['lms_employee_code'];
					$managerApproved = 'Approve';
					if($user['employee_daily_journey_detail_manager_remark']!=''){
						$managerApproved = 'Reject';
					}
				}
				# FINANCE MANAGER
				$financeManagerCode = '';
				$financeApproved = 'Pending';
				if($user['employee_daily_journey_detail_finance_datetime']!=''){
					$obj_empB = $this->app->load_model("employee");
					$empB = $obj_empB->execute("SELECT", false, "", "id='".$user['employee_daily_journey_detail_finance_employee_id']."'");
					$financeManagerCode = $empB[0]['lms_employee_code'];
					$financeApproved = 'Approve';
					if($user['employee_daily_journey_detail_finance_remark']!=''){
						$financeApproved = 'Reject';
					}
				}

				$amount=$user['total_km']>0?($user['total_km']*$user['employee_detail_per_km']):0;

				$data_array[]=array(
					"ID"=>$user['id'],
					"Employee Name"=>$user['employee_name'],
					"Employee Code"=>$user['employee_lms_employee_code'],
					"Employee Mobile"=>$user['employee_mobile'],
					"Date"=>date('d-m-Y',strtotime($user['journey_date'])),
					"Start Time"=>date('h:i A',strtotime($user['start_datetime'])),
					"End Time"=>date('h:i A',strtotime($user['end_datetime'])),
					"Start KM"=>$user['start_km'],
					"End KM"=>$user['end_km'],
					"Total KM"=>$user['total_km'],
					"Total RS"=>$amount,
					"Status"=>$user['status'],
					"Manager Approved"=>$managerApproved,
					"Manager Code"=>$managerCode,
					"Manager Approve Time"=>$user['employee_daily_journey_detail_manager_datetime']!=''?date('d-m-Y h:i A',strtotime($user['employee_daily_journey_detail_manager_datetime'])):'',
					"Finance Manager Approved"=>$financeApproved,
					"Finance Manager Code"=>$financeManagerCode,
					"Finance Manager Approve Time"=>$user['employee_daily_journey_detail_finance_datetime']!=''?date('d-m-Y h:i A',strtotime($user['employee_daily_journey_detail_finance_datetime'])):'',
				);
				$ucount++;
			}
			
			$filename="journey-list-".date('d-m-Y');
			$this->app->utility->export_excel($ExeclHeads,$data_array,$ExeclHeads,$filename,$ExeclHeads);
		}
		
	}
}
?>