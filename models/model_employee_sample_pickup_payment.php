<?php
	class model_employee_sample_pickup_payment{
		public $fields= array();
		public $nullable= array();
		public $default_value= array();
		public $ID= 0;
		public $KEY= "";

		function __construct($ID=0){
			$this->ID = $ID;
			$this->KEY = "id";
			$this->fields["id"]="int(11)";
			$this->nullable["id"]="NO";
			$this->default_value["id"]="";
			$this->fields["employee_id"]="bigint(20)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["lis_transaction_id"]="varchar(255)";
			$this->nullable["lis_transaction_id"]="NO";
			$this->default_value["lis_transaction_id"]="";
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="Yes";
			$this->default_value["client_id"]="";
			$this->fields["transaction_id"]="varchar(255)";
			$this->nullable["transaction_id"]="Yes";
			$this->default_value["transaction_id"]="";
			$this->fields["amount"]="float(9,2)";
			$this->nullable["amount"]="Yes";
			$this->default_value["amount"]="";
			$this->fields["transaction_date"]="varchar(255)";
			$this->nullable["transaction_date"]="Yes";
			$this->default_value["transaction_date"]="";
			$this->fields["payment_status"]="enum('Success','Fail')";
			$this->nullable["payment_status"]="NO";
			$this->default_value["payment_status"]="Fail";
			$this->fields["remark"]="varchar(255)";
			$this->nullable["remark"]="Yes";
			$this->default_value["remark"]="";
			$this->fields["ip"]="varchar(255)";
			$this->nullable["ip"]="Yes";
			$this->default_value["ip"]="";
			$this->fields["browser"]="varchar(255)";
			$this->nullable["browser"]="Yes";
			$this->default_value["browser"]="";
			$this->fields["created_at"]="datetime";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="";
		}
	}
?>