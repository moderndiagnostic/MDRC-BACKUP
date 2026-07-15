<?php
	class model_employee_task_master{
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
			$this->fields["purpose"]="varchar(255)";
			$this->nullable["purpose"]="NO";
			$this->default_value["purpose"]="";
			$this->fields["employee_primary_id"]="int(11)";
			$this->nullable["employee_primary_id"]="NO";
			$this->default_value["employee_primary_id"]="";
			$this->fields["employee_ids"]="varchar(255)";
			$this->nullable["employee_ids"]="NO";
			$this->default_value["employee_ids"]="";
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="NO";
			$this->default_value["client_id"]="";
			$this->fields["task_datetime"]="datetime";
			$this->nullable["task_datetime"]="YES";
			$this->default_value["task_datetime"]="";
			$this->fields["task_remark"]="varchar(255)";
			$this->nullable["task_remark"]="NO";
			$this->default_value["task_remark"]="";
			$this->fields["status"]="enum('Draft','Active','Completed','Inprogress','Canceled','Trash')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Active";
			$this->fields["assign_by_employee_id"]="int(11)";
			$this->nullable["assign_by_employee_id"]="NO";
			$this->default_value["assign_by_employee_id"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="0000-00-00 00:00:00";
		}
	}
?>