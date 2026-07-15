<?php
	class model_employee_detail{
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
			$this->fields["area"]="varchar(255)";
			$this->nullable["area"]="NO";
			$this->default_value["area"]="";
			$this->fields["master_centre_lms_ids"]="text";
			$this->nullable["master_centre_lms_ids"]="NO";
			$this->default_value["master_centre_lms_ids"]="";
			$this->fields["per_km"]="float(9,2)";
			$this->nullable["per_km"]="NO";
			$this->default_value["per_km"]="0.00";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="current_timestamp()";
		}
	}
?>