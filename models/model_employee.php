<?php
	class model_employee{
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
			$this->fields["lms_employee_id"]="bigint(20)";
			$this->nullable["lms_employee_id"]="NO";
			$this->default_value["lms_employee_id"]="";
			$this->fields["lms_employee_code"]="varchar(255)";
			$this->nullable["lms_employee_code"]="NO";
			$this->default_value["lms_employee_code"]="";
			$this->fields["image"]="varchar(255)";
			$this->nullable["image"]="NO";
			$this->default_value["image"]="";
			$this->fields["name"]="varchar(255)";
			$this->nullable["name"]="NO";
			$this->default_value["name"]="";
			$this->fields["email"]="varchar(255)";
			$this->nullable["email"]="NO";
			$this->default_value["email"]="";
			$this->fields["mobile"]="varchar(255)";
			$this->nullable["mobile"]="NO";
			$this->default_value["mobile"]="";
			$this->fields["master_designation_id"]="int(11)";
			$this->nullable["master_designation_id"]="NO";
			$this->default_value["master_designation_id"]="";
			$this->fields["city_id"]="int(11)";
			$this->nullable["city_id"]="NO";
			$this->default_value["city_id"]="";
			$this->fields["login_password"]="varchar(255)";
			$this->nullable["login_password"]="NO";
			$this->default_value["login_password"]="";
			$this->fields["status"]="enum('Active','Inactive','Trash')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Active";
			$this->fields["reporting_employee_lms_id"]="int(11)";
			$this->nullable["reporting_employee_lms_id"]="NO";
			$this->default_value["reporting_employee_lms_id"]="";
			$this->fields["fcm_token"]="text";
			$this->nullable["fcm_token"]="NO";
			$this->default_value["fcm_token"]="";
			$this->fields["app_version"]="varchar(50)";
			$this->nullable["app_version"]="NO";
			$this->default_value["app_version"]="";
			$this->fields["employee_role"]="enum('Admin','Employee')";
			$this->nullable["employee_role"]="NO";
			$this->default_value["employee_role"]="Employee";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="current_timestamp()";
		}
	}
?>