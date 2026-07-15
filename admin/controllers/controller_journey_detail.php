<?php
class _journey_detail extends controller
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
		$id=$this->app->getGetVar('id');
		if($id=='')
		{
			$this->app->redirect("index.php?view=journey_list");
		}

		$obj_employee_daily_journey = $this->app->load_model("employee_daily_journey");
		$obj_employee_daily_journey->join_table("employee", "left", array("name","status","email","mobile"), array("employee_id"=>"id"));
		$obj_employee_daily_journey->join_table("employee_daily_journey_detail", "left", array(), array("id"=>"employee_daily_journey_id"));
		$data_employee_daily_journey = $obj_employee_daily_journey->execute("SELECT",false,"","employee_daily_journey.id='".$id."'");

		## GET SAMPLE COLLECT DATA
		$obj_sample_pickup = $this->app->load_model("employee_sample_pickup");
		$obj_sample_pickup->join_table("client", "left", array("company_name","email","mobile"), array("client_id"=>"id"));
		$rs_sample_pickup = $obj_sample_pickup->execute("SELECT",false,"","employee_sample_pickup.employee_id='".$data_employee_daily_journey[0]['employee_id']."' and employee_sample_pickup.pickup_date='".date('d-m-Y',strtotime($data_employee_daily_journey[0]['journey_date']))."'","employee_sample_pickup.id asc");

		$this->app->assign("data_sample", $rs_sample_pickup);

		if(!empty($data_employee_daily_journey[0]['employee_daily_journey_detail_manager_employee_id'])){
			$obj_emp = $this->app->load_model("employee");
			$manager_employee_name = $obj_emp->execute("SELECT",false,"","id='".$data_employee_daily_journey[0]['employee_daily_journey_detail_manager_employee_id']."'");
			$this->app->assign("manager_name", $manager_employee_name[0]['name']);
		} else {
			$this->app->assign("manager_name", "");
		}

		if(!empty($data_employee_daily_journey[0]['employee_daily_journey_detail_finance_employee_id'])){
			$obj_emp2 = $this->app->load_model("employee");
			$manager_employee_name2 = $obj_emp2->execute("SELECT",false,"","id='".$data_employee_daily_journey[0]['employee_daily_journey_detail_finance_employee_id']."'");
			$this->app->assign("finance_name", $manager_employee_name2[0]['name']);
		} else {
			$this->app->assign("finance_name", "");
		}

		if(count($data_employee_daily_journey)>0)
		{
			$this->app->assign("journey_data", $data_employee_daily_journey[0]);

			if($data_employee_daily_journey[0]['status'] == 'Running'){
				$status = '<span class="badge badge-primary">Running</span>';
			} elseif($data_employee_daily_journey[0]['status'] == 'Pending'){
				$status = '<span class="badge badge-warning">Pending</span>';
			} elseif($data_employee_daily_journey[0]['status'] == 'Approve By Manager'){
				$status = '<span class="badge badge-success">Approve By Manager</span>';
			} elseif($data_employee_daily_journey[0]['status'] == 'Approve By Finance'){
				$status = '<span class="badge badge-success">Approve By Finance</span>';
			} elseif($data_employee_daily_journey[0]['status'] == 'Reject By Manager'){
				$status = '<span class="badge badge-danger">Reject By Manager</span>';
			} elseif($data_employee_daily_journey[0]['status'] == 'Reject By Finance'){
				$status = '<span class="badge badge-danger">Reject By Finance</span>';
			}
			$this->app->assign("statusBadge", $status);

			if($data_employee_daily_journey[0]['employee_status'] == 'Active'){
				$emp_status = '<span class="alert alert-success pd-label-1 rounded-pill mb-0 btn-sm py-1 px-3">Active</span>';
			} else {
				$emp_status = '<span class="alert alert-danger pd-label-1 rounded-pill mb-0 btn-sm py-1 px-3">Inactive</span>';
			}
			$this->app->assign("emp_status", $emp_status);

			$obj_employee_daily_journey_detail = $this->app->load_model("employee_daily_journey_logs");
			$obj_employee_daily_journey->join_table("employee_daily_journey", "left", array(), array("employee_daily_journey_id"=>"id"));
			$data_employee_daily_journey_detail = $obj_employee_daily_journey_detail->execute("SELECT",false,"","employee_daily_journey_logs.employee_daily_journey_id='".$id."'");

			$this->app->assign("journey_detail", $data_employee_daily_journey_detail);
		}
		else
		{
			$this->app->redirect("index.php?view=journey_list");
		}
	}
}
?>