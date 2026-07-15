<?php
	class model_employee_sample_dispatch_other_detail{
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
			$this->fields["employee_sample_dispatch_id"]="int(11)";
			$this->nullable["employee_sample_dispatch_id"]="NO";
			$this->default_value["employee_sample_dispatch_id"]="";
			$this->fields["courier_delivery_time"]="varchar(255)";
			$this->nullable["courier_delivery_time"]="NO";
			$this->default_value["courier_delivery_time"]="";
			$this->fields["package_photo"]="varchar(255)";
			$this->nullable["package_photo"]="NO";
			$this->default_value["package_photo"]="";
			$this->fields["receipt_photo"]="varchar(255)";
			$this->nullable["receipt_photo"]="NO";
			$this->default_value["receipt_photo"]="";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="current_timestamp()";
		}
	}
?>