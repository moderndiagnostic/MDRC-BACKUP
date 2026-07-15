<?php
	class model_employee_leave_assign_client{
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
			$this->fields["employee_leave_id"]="int(11)";
			$this->nullable["employee_leave_id"]="NO";
			$this->default_value["employee_leave_id"]="";
			$this->fields["assign_date"]="date";
			$this->nullable["assign_date"]="NO";
			$this->default_value["assign_date"]="";
			$this->fields["client_ids"]="varchar(255)";
			$this->nullable["client_ids"]="NO";
			$this->default_value["client_ids"]="";
			$this->fields["employee_id"]="bigint(20)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["assign_by_employee_id"]="bigint(20)";
			$this->nullable["assign_by_employee_id"]="NO";
			$this->default_value["assign_by_employee_id"]="";
			$this->fields["status"]="enum('Active','Trash')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Active";
			$this->fields["created_at"]="datetime";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
		}
	}
?>