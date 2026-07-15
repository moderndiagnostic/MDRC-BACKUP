<?php
	class model_employee_centre{
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
			$this->fields["lms_centre_id"]="int(11)";
			$this->nullable["lms_centre_id"]="NO";
			$this->default_value["lms_centre_id"]="";
		}
	}
?>