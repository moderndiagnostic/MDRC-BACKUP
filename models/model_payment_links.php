<?php
	class model_payment_links{
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
			$this->fields["success_payment_link_transaction_id"]="int(11)";
			$this->nullable["success_payment_link_transaction_id"]="NO";
			$this->default_value["success_payment_link_transaction_id"]="";
			$this->fields["name"]="varchar(255)";
			$this->nullable["name"]="NO";
			$this->default_value["name"]="";
			$this->fields["email"]="varchar(255)";
			$this->nullable["email"]="NO";
			$this->default_value["email"]="";
			$this->fields["mobile"]="varchar(255)";
			$this->nullable["mobile"]="NO";
			$this->default_value["mobile"]="";
			$this->fields["amount"]="float(9,2)";
			$this->nullable["amount"]="NO";
			$this->default_value["amount"]="";
			$this->fields["image"]="varchar(255)";
			$this->nullable["image"]="NO";
			$this->default_value["image"]="";
			$this->fields["remark"]="date";
			$this->nullable["remark"]="text";
			$this->default_value["remark"]="";
			$this->fields["status"]="enum('Pending','Success','Fail')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Pending";
			$this->fields["created_at"]="datetime";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="";
		}
	}
?>