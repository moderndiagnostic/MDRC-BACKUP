<?php
class _employee_sample_pickup_payment_list extends controller
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
		$obj_employee=$this->app->load_model("employee");
		$rs_employee = $obj_employee->execute("SELECT", false, "", "status='Active' and employee_role='Employee'");	

		$employee[]="Select";
		foreach($rs_employee as $item){
		$employee[$item['id']]=$item["name"];
		}
		$this->app->assign("employee",$employee);
	}	
		
	function load_data()
	{
	}	

	function export_data()
	{
		$start_date=$this->app->getGetVar('start_date');
		$end_date=$this->app->getGetVar('end_date');
		$employee_id=$this->app->getGetVar('employee_id');

		$searchQuery='';
		if($start_date!='')
		{
			$searchQuery.=" AND DATE(employee_sample_pickup_payment.transaction_date) BETWEEN STR_TO_DATE('".$start_date."', '%d-%m-%Y') AND STR_TO_DATE('".$end_date."', '%d-%m-%Y')";
		}
		if($employee_id!='' && $employee_id!=0)
		{
			$searchQuery.=" AND employee_sample_pickup_payment.employee_id='".$employee_id."'";
		}
		
		$this->app->no_html=true;
		$obj_excel = $this->app->load_module("PHPExcel");
		$ExeclHeads=array(
			"Client Name",
			"Client Mobile",
			"Employee Name",
			"Employee Designation",
			"Employee Code",
			"Amount",
			"PAYU Transaction ID",
			"LIS Transaction ID",
			"Transaction Date",
			"Payment Status"
		);

		$obj_brand=$this->app->load_model("employee_sample_pickup_payment");
		$obj_brand->join_table("client", "left", array(), array("client_id"=>"id"));
		$obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
		$obj_brand->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
		$result = $obj_brand->execute("SELECT", false, "", "employee_sample_pickup_payment.id!='0'  ".$searchQuery."");

		for($i=0;$i<count($result);$i++)
		{
			$user_array[]=array(
				"Client Name"=>$result[$i]['client_company_name'],
				"Client Mobile"=>$result[$i]['client_mobile'],
				"Employee Name"=>$result[$i]['employee_name'],
				"Employee Designation"=>$result[$i]['master_designation_name'],
				"Employee Code"=>$result[$i]['employee_lms_employee_code'],
				"Amount"=>$result[$i]['amount'],
				"PAYU Transaction ID"=>$result[$i]['transaction_id'],
				"LIS Transaction ID"=>$result[$i]['lis_transaction_id'],
				"Transaction Date"=>date('d-m-y H:i:s',strtotime($result[$i]['transaction_date'])),
				"Payment Status"=>$result[$i]['payment_status']
			);
		}
		$array_field=array(
		"block_name"=>array("options"=>'',"prompt_title"=>'',"prompt"=>''),
		"flat_type"=>array("options"=>"","prompt_title"=>"","prompt"=>""),
		"resident_type"=>array("options"=>'',"prompt_title"=>"","prompt"=>"")
		);
		$data_array=$user_array;
		$fields=array(
			"Client Name",
			"Client Mobile",
			"Employee Name",
			"Employee Designation",
			"Employee Code",
			"Amount",
			"PAYU Transaction ID",
			"LIS Transaction ID",
			"Transaction Date",
			"Payment Status"
		);
		$filename="employee_sample_pickup_payment_list - ".date('d-m-Y');
		$this->app->utility->export_excel($ExeclHeads,$data_array,$fields,$filename,$array_field);
	}
}	
?>