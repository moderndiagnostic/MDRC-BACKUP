<?php
	class model_employee_punch_inout{
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
			$this->fields["punch_date"]="date";
			$this->nullable["punch_date"]="NO";
			$this->default_value["punch_date"]="";
			$this->fields["punchin_datetime"]="datetime";
			$this->nullable["punchin_datetime"]="NO";
			$this->default_value["punchin_datetime"]="";
			$this->fields["punchout_datetime"]="datetime";
			$this->nullable["punchout_datetime"]="YES";
			$this->default_value["punchout_datetime"]="";
			$this->fields["employee_photo"]="varchar(255)";
			$this->nullable["employee_photo"]="NO";
			$this->default_value["employee_photo"]="";
			$this->fields["latitude"]="varchar(255)";
			$this->nullable["latitude"]="NO";
			$this->default_value["latitude"]="";
			$this->fields["longitude"]="varchar(255)";
			$this->nullable["longitude"]="NO";
			$this->default_value["longitude"]="";
			$this->fields["map_address"]="varchar(255)";
			$this->nullable["map_address"]="NO";
			$this->default_value["map_address"]="";
			$this->fields["pincode"]="varchar(255)";
			$this->nullable["pincode"]="NO";
			$this->default_value["pincode"]="";
		}
	}
?>