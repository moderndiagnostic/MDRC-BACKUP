<?php
	class model_hrms_employee_expenses{
		public $fields= array();
		public $nullable= array();
		public $default_value= array();
		public $ID= 0;
		public $KEY= "";

		function __construct($ID=0){
			$this->ID = $ID;
			$this->KEY = "id";
			$this->fields["id"]="bigint(20)";
			$this->nullable["id"]="NO";
			$this->default_value["id"]="";
			$this->fields["employee_id"]="int(11)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["hrms_expense_id"]="int(11)";
			$this->nullable["hrms_expense_id"]="NO";
			$this->default_value["hrms_expense_id"]="";

			$this->fields["category"]="varchar(255)";
			$this->nullable["category"]="NO";
			$this->default_value["category"]="";
			$this->fields["expense_type"]="varchar(255)";
			$this->nullable["expense_type"]="NO";
			$this->default_value["expense_type"]="";
			$this->fields["approved_amount"]="float(9,2)";
			$this->nullable["approved_amount"]="NO";
			$this->default_value["approved_amount"]="";
			$this->fields["finance_amount"]="float(9,2)";
			$this->nullable["finance_amount"]="NO";
			$this->default_value["finance_amount"]="";

			$this->fields["total_km"]="float(9,2)";
			$this->nullable["total_km"]="NO";
			$this->default_value["total_km"]="";
			$this->fields["status"]="varchar(255)";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="";
			$this->fields["finance_approved"]="varchar(255)";
			$this->nullable["finance_approved"]="NO";
			$this->default_value["finance_approved"]="";
			$this->fields["trans_no"]="varchar(255)";
			$this->nullable["trans_no"]="NO";
			$this->default_value["trans_no"]="";

			$this->fields["vendor_name"]="varchar(255)";
			$this->nullable["vendor_name"]="NO";
			$this->default_value["vendor_name"]="";
			$this->fields["bill_no"]="varchar(255)";
			$this->nullable["bill_no"]="NO";
			$this->default_value["bill_no"]="";
			$this->fields["mode"]="varchar(255)";
			$this->nullable["mode"]="NO";
			$this->default_value["mode"]="";
			$this->fields["purpose"]="varchar(255)";
			$this->nullable["purpose"]="NO";
			$this->default_value["purpose"]="";

			$this->fields["remark"]="varchar(255)";
			$this->nullable["remark"]="NO";
			$this->default_value["remark"]="";
			$this->fields["manager1"]="varchar(255)";
			$this->nullable["manager1"]="NO";
			$this->default_value["manager1"]="";
			$this->fields["manager2"]="varchar(255)";
			$this->nullable["manager2"]="NO";
			$this->default_value["manager2"]="";
			$this->fields["manager3"]="varchar(255)";
			$this->nullable["manager3"]="NO";
			$this->default_value["manager3"]="";

			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["expense_date"]="date";
			$this->nullable["expense_date"]="NO";
			$this->default_value["expense_date"]="";
		}
	}
?>