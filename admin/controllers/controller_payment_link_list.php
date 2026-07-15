<?php
class _payment_link_list extends controller
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
		$this->app->no_html=true;
		$obj_excel = $this->app->load_module("PHPExcel");
		$ExeclHeads=array("Employee Name","Client Name","Amount","Mobile","Payment Time","Payment Status");

		
		$obj_brand=$this->app->load_model("payment_links");
		$obj_brand->join_table("payment_link_transaction", "left", array(), array("id"=>"payment_link_id"));
		$obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
		$obj_brand->join_table(["employee"=>"employee","master_designation"=>"master_designation"], "left", array(), array("master_designation_id"=>"id"));
		$result = $obj_brand->execute("SELECT", false, "", "","");	
		for($i=0;$i<count($result);$i++)
		{
			$user_array[]=array("Employee Name"=>$result[$i]['employee_name'],"Client Name"=>$result[$i]['name'],"Amount"=>$result[$i]['amount'],"Mobile"=>$result[$i]['mobile'],"Payment Time"=>date('d-m-y H:i:s',strtotime($result[$i]['payment_link_transaction_created_at'])),"Payment Status"=>$result[$i]['payment_link_transaction_status']);
		}
		$array_field=array(
		"block_name"=>array("options"=>'',"prompt_title"=>'',"prompt"=>''),
		"flat_type"=>array("options"=>"","prompt_title"=>"","prompt"=>""),
		"resident_type"=>array("options"=>'',"prompt_title"=>"","prompt"=>"")
		);
		$data_array=$user_array;
		$fields=array("Employee Name","Client Name","Amount","Mobile","Payment Time","Payment Status");
		$filename="payment_link_list - ".date('d-m-Y');
		$this->app->utility->export_excel($ExeclHeads,$data_array,$fields,$filename,$array_field);
	}
}	
?>