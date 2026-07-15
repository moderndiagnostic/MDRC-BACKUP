<?php
	class model_employee_sample_pickup{
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
			$this->fields["employee_id"]="bigint(20)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="NO";
			$this->default_value["client_id"]="";
			$this->fields["pickup_date"]="varchar(100)";
			$this->nullable["pickup_date"]="NO";
			$this->default_value["pickup_date"]="";
			$this->fields["pickup_type"]="enum('auto','manual')";
			$this->nullable["pickup_type"]="NO";
			$this->default_value["pickup_type"]="auto";
			$this->fields["status"]="enum('Pending','In Progress','Completed')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Pending";
			$this->fields["collect_sample"]="enum('Yes','No')";
			$this->nullable["collect_sample"]="NO";
			$this->default_value["collect_sample"]="No";
			$this->fields["collect_payment"]="enum('Yes','No')";
			$this->nullable["collect_payment"]="NO";
			$this->default_value["collect_payment"]="No";
			$this->fields["sample_count"]="int(11)";
			$this->nullable["sample_count"]="NO";
			$this->default_value["sample_count"]="";
			$this->fields["payment_amount"]="float(9,2)";
			$this->nullable["payment_amount"]="NO";
			$this->default_value["payment_amount"]="";
			$this->fields["hub_received"]="enum('Yes','No')";
			$this->nullable["hub_received"]="NO";
			$this->default_value["hub_received"]="No";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="0000-00-00 00:00:00";
			$this->fields["device_type"]="enum('Web','Android','IOS')";
			$this->nullable["device_type"]="NO";
			$this->default_value["device_type"]="Android";
			$this->fields["ip"]="varchar(20)";
			$this->nullable["ip"]="NO";
			$this->default_value["ip"]="";
			$this->fields["distance_km"]="varchar(20)";
			$this->nullable["distance_km"]="NO";
			$this->default_value["distance_km"]="";
		}
	}
?>