<?php
	class model_employee_sample_pickup_payment_lis_calls {
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
			$this->fields["employee_sample_pickup_payment_id"]="bigint(20)";
			$this->nullable["employee_sample_pickup_payment_id"]="NO";
			$this->default_value["employee_sample_pickup_payment_id"]="";
			$this->fields["admin_id"]="int(11)";
			$this->nullable["admin_id"]="NO";
			$this->default_value["admin_id"]="";
			$this->fields["request_json"]="longtext";
			$this->nullable["request_json"]="Yes";
			$this->default_value["request_json"]="";
			$this->fields["response_json"]="longtext";
			$this->nullable["response_json"]="Yes";
			$this->default_value["response_json"]="";
			$this->fields["ip"]="varchar(255)";
			$this->nullable["ip"]="NO";
			$this->default_value["ip"]="";
			$this->fields["created_at"]="varchar(255)";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="";
		}
	}
?>