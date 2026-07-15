<?php
	class model_payment_link_transaction{
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
			$this->fields["payment_link_id"]="int(11)";
			$this->nullable["payment_link_id"]="NO";
			$this->default_value["payment_link_id"]="";
			$this->fields["transaction_id"]="varchar(255)";
			$this->nullable["transaction_id"]="Yes";
			$this->default_value["transaction_id"]="";
			$this->fields["remark"]="text";
			$this->nullable["remark"]="Yes";
			$this->default_value["remark"]="";
			$this->fields["status"]="enum('Pending','Success','Fail')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Fail";
			$this->fields["ip"]="varchar(255)";
			$this->nullable["ip"]="NO";
			$this->default_value["ip"]="";
			$this->fields["created_at"]="datetime";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="";
		}
	}
?>