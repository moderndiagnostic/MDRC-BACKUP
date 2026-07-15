<?php
	class model_employee_sample_pickup_images{
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
			$this->fields["employee_sample_pickup_id"]="bigint(20)";
			$this->nullable["employee_sample_pickup_id"]="NO";
			$this->default_value["employee_sample_pickup_id"]="";
			$this->fields["parent_id"]="int(20)";
			$this->nullable["parent_id"]="NO";
			$this->default_value["parent_id"]="0";
			$this->fields["master_no"]="int(20)";
			$this->nullable["master_no"]="NO";
			$this->default_value["master_no"]="0";
			$this->fields["employee_id"]="bigint(20)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="NO";
			$this->default_value["client_id"]="";
			$this->fields["image"]="varchar(255)";
			$this->nullable["image"]="NO";
			$this->default_value["image"]="";
			$this->fields["barcode"]="varchar(255)";
			$this->nullable["barcode"]="NO";
			$this->default_value["barcode"]="";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="current_timestamp()";
			$this->fields["device_type"]="enum('Web','Android','IOS')";
			$this->nullable["device_type"]="NO";
			$this->default_value["device_type"]="Android";
			$this->fields["remark"]="varchar(255)";
			$this->nullable["remark"]="NO";
			$this->default_value["remark"]="";
			$this->fields["ip"]="varchar(20)";
			$this->nullable["ip"]="NO";
			$this->default_value["ip"]="";
		}
	}
?>