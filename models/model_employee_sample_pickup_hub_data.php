<?php
	class model_employee_sample_pickup_hub_data{
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
			$this->fields["employee_sample_pickup_id"]="int(11)";
			$this->nullable["employee_sample_pickup_id"]="NO";
			$this->default_value["employee_sample_pickup_id"]="";
			$this->fields["received_employee_id"]="int(11)";
			$this->nullable["received_employee_id"]="NO";
			$this->default_value["received_employee_id"]="";
			$this->fields["master_centre_id"]="int(11)";
			$this->nullable["master_centre_id"]="NO";
			$this->default_value["master_centre_id"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
		}
	}
?>