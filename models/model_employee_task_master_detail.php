<?php
	class model_employee_task_master_detail{
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
			$this->fields["employee_task_master_id"]="bigint(20)";
			$this->nullable["employee_task_master_id"]="NO";
			$this->default_value["employee_task_master_id"]="";
			$this->fields["check_in"]="datetime";
			$this->nullable["check_in"]="YES";
			$this->default_value["check_in"]="";
			$this->fields["check_out"]="datetime";
			$this->nullable["check_out"]="YES";
			$this->default_value["check_out"]="";
			$this->fields["meeting"]="datetime";
			$this->nullable["meeting"]="YES";
			$this->default_value["meeting"]="";
			$this->fields["meeting_status"]="enum('Complete','Cancel')";
			$this->nullable["meeting_status"]="NO";
			$this->default_value["meeting_status"]="Complete";
			$this->fields["meeting_client_meet"]="enum('Yes','No')";
			$this->nullable["meeting_client_meet"]="YES";
			$this->default_value["meeting_client_meet"]="Yes";
			$this->fields["task_type"]="enum('Assign','Manual')";
			$this->nullable["task_type"]="NO";
			$this->default_value["task_type"]="Assign";
			$this->fields["client_ids"]="varchar(255)";
			$this->nullable["client_ids"]="NO";
			$this->default_value["client_ids"]="";

			$this->fields["distance_km"]="varchar(255)";
			$this->nullable["distance_km"]="NO";
			$this->default_value["distance_km"]="";

		}
	}
?>