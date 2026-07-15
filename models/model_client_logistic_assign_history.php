<?php
	class model_client_logistic_assign_history{
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
			$this->fields["client_logistic_assign_id"]="int(11)";
			$this->nullable["client_logistic_assign_id"]="NO";
			$this->default_value["client_logistic_assign_id"]="";
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="NO";
			$this->default_value["client_id"]="";
			$this->fields["logistic_manager_employee_id"]="int(11)";
			$this->nullable["logistic_manager_employee_id"]="NO";
			$this->default_value["logistic_manager_employee_id"]="";
			$this->fields["employee_id"]="bigint(20)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["assign_by_employee_id"]="bigint(20)";
			$this->nullable["assign_by_employee_id"]="NO";
			$this->default_value["assign_by_employee_id"]="";
			$this->fields["action"]="varchar(255)";
			$this->nullable["action"]="NO";
			$this->default_value["action"]="";
			$this->fields["title"]="varchar(255)";
			$this->nullable["title"]="NO";
			$this->default_value["title"]="";
			$this->fields["remark"]="varchar(255)";
			$this->nullable["remark"]="NO";
			$this->default_value["remark"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
		}
	}
?>