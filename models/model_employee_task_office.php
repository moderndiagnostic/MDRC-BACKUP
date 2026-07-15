<?php
	class model_employee_task_office{
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
			$this->fields["check_in"]="datetime";
			$this->nullable["check_in"]="NO";
			$this->default_value["check_in"]="";
			$this->fields["check_out"]="datetime";
			$this->nullable["check_out"]="NO";
			$this->default_value["check_out"]="";
			$this->fields["task_remark"]="varchar(500)";
			$this->nullable["task_remark"]="NO";
			$this->default_value["task_remark"]="";
			$this->fields["status"]="enum('Completed','Canceled','Trash')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Completed";
			$this->fields["latitude"]="varchar(100)";
			$this->nullable["latitude"]="NO";
			$this->default_value["latitude"]="";
			$this->fields["longitude"]="varchar(100)";
			$this->nullable["longitude"]="NO";
			$this->default_value["longitude"]="";
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
		}
	}
?>