<?php
	class model_employee_task_master_update{
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
			$this->fields["employee_id"]="int(11)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["activity"]="enum('Check In','Check Out','Meeting','Start Journey','End Journey')";
			$this->nullable["activity"]="NO";
			$this->default_value["activity"]="Check In";
			$this->fields["checkin_photo"]="varchar(255)";
			$this->nullable["checkin_photo"]="NO";
			$this->default_value["checkin_photo"]="";
			$this->fields["meeting_remark"]="text";
			$this->nullable["meeting_remark"]="NO";
			$this->default_value["meeting_remark"]="";
			$this->fields["meeting_status"]="varchar(255)";
			$this->nullable["meeting_status"]="NO";
			$this->default_value["meeting_status"]="";
			$this->fields["meeting_client_meet"]="enum('Yes','No')";
			$this->nullable["meeting_client_meet"]="YES";
			$this->default_value["meeting_client_meet"]="Yes";
			$this->fields["activity_time"]="timestamp";
			$this->nullable["activity_time"]="NO";
			$this->default_value["activity_time"]="current_timestamp()";
			$this->fields["latitude"]="varchar(255)";
			$this->nullable["latitude"]="NO";
			$this->default_value["latitude"]="";
			$this->fields["longitude"]="varchar(255)";
			$this->nullable["longitude"]="NO";
			$this->default_value["longitude"]="";
			$this->fields["google_address"]="varchar(255)";
			$this->nullable["google_address"]="NO";
			$this->default_value["google_address"]="";
		}
	}
?>