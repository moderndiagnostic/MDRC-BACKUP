<?php
	class model_employee_leave{
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
			$this->fields["employee_id"]="bigint(20)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["leave_start"]="date";
			$this->nullable["leave_start"]="NO";
			$this->default_value["leave_start"]="";
			$this->fields["leave_end"]="date";
			$this->nullable["leave_end"]="NO";
			$this->default_value["leave_end"]="";
			$this->fields["reason"]="text";
			$this->nullable["reason"]="NO";
			$this->default_value["reason"]="";
			$this->fields["status"]="enum('Pending','Approved','Reject')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Pending";
			$this->fields["created_at"]="datetime";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="";
			$this->fields["update_remark"]="text";
			$this->nullable["update_remark"]="NO";
			$this->default_value["update_remark"]="";
			$this->fields["update_by_employee_id"]="bigint(20)";
			$this->nullable["update_by_employee_id"]="NO";
			$this->default_value["update_by_employee_id"]="";
			$this->fields["status_updated_at"]="datetime";
			$this->nullable["status_updated_at"]="YES";
			$this->default_value["status_updated_at"]="";
		}
	}
?>