<?php
	class model_client_detail{
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
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="NO";
			$this->default_value["client_id"]="";
			$this->fields["address"]="text";
			$this->nullable["address"]="NO";
			$this->default_value["address"]="";
			$this->fields["area"]="varchar(255)";
			$this->nullable["area"]="NO";
			$this->default_value["area"]="";
			$this->fields["ledger_report_password"]="varchar(255)";
			$this->nullable["ledger_report_password"]="NO";
			$this->default_value["ledger_report_password"]="";
			$this->fields["invoice_to_center"]="varchar(255)";
			$this->nullable["invoice_to_center"]="NO";
			$this->default_value["invoice_to_center"]="";
			$this->fields["booking_lock_reason"]="varchar(255)";
			$this->nullable["booking_lock_reason"]="NO";
			$this->default_value["booking_lock_reason"]="";
			$this->fields["credit_limit"]="varchar(255)";
			$this->nullable["credit_limit"]="NO";
			$this->default_value["credit_limit"]="";
			$this->fields["is_printing_lock"]="varchar(255)";
			$this->nullable["is_printing_lock"]="NO";
			$this->default_value["is_printing_lock"]="";
			$this->fields["business_type"]="varchar(255)";
			$this->nullable["business_type"]="NO";
			$this->default_value["business_type"]="";
			$this->fields["specialization"]="varchar(255)";
			$this->nullable["specialization"]="NO";
			$this->default_value["specialization"]="";
			$this->fields["compnay_legal"]="varchar(255)";
			$this->nullable["compnay_legal"]="NO";
			$this->default_value["compnay_legal"]="";
			$this->fields["sample_pickup"]="varchar(255)";
			$this->nullable["sample_pickup"]="NO";
			$this->default_value["sample_pickup"]="";
			$this->fields["sample_pickup_frequency"]="varchar(255)";
			$this->nullable["sample_pickup_frequency"]="NO";
			$this->default_value["sample_pickup_frequency"]="";
			$this->fields["payment_mode"]="varchar(255)";
			$this->nullable["payment_mode"]="NO";
			$this->default_value["payment_mode"]="";
			$this->fields["invoice_billing_cycle"]="varchar(255)";
			$this->nullable["invoice_billing_cycle"]="NO";
			$this->default_value["invoice_billing_cycle"]="";
			$this->fields["register_type"]="varchar(255)";
			$this->nullable["register_type"]="NO";
			$this->default_value["register_type"]="";
			$this->fields["added_by_employee_id"]="bigint(20)";
			$this->nullable["added_by_employee_id"]="NO";
			$this->default_value["added_by_employee_id"]="";
		}
	}
?>