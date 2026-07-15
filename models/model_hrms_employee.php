<?php
	class model_hrms_employee{
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
			$this->fields["hrms_id"]="int(11)";
			$this->nullable["hrms_id"]="NO";
			$this->default_value["hrms_id"]="";
			$this->fields["hrms_status"]="int(11)";
			$this->nullable["hrms_status"]="NO";
			$this->default_value["hrms_status"]="";
			$this->fields["name"]="varchar(255)";
			$this->nullable["name"]="NO";
			$this->default_value["name"]="";
			$this->fields["mdrc_code"]="varchar(255)";
			$this->nullable["mdrc_code"]="NO";
			$this->default_value["mdrc_code"]="";
			$this->fields["location"]="varchar(255)";
			$this->nullable["location"]="NO";
			$this->default_value["location"]="";
			$this->fields["department"]="varchar(255)";
			$this->nullable["department"]="NO";
			$this->default_value["department"]="";
			$this->fields["cost_center"]="varchar(255)";
			$this->nullable["cost_center"]="NO";
			$this->default_value["cost_center"]="";
			$this->fields["designation"]="varchar(255)";
			$this->nullable["designation"]="NO";
			$this->default_value["designation"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="current_timestamp()";
		}
	}
?>