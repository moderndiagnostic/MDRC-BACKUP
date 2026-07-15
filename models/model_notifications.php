<?php
	class model_notifications{
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
			$this->fields["noti_type"]="varchar(255)";
			$this->nullable["noti_type"]="NO";
			$this->default_value["noti_type"]="";
			$this->fields["title"]="varchar(255)";
			$this->nullable["title"]="NO";
			$this->default_value["title"]="";
			$this->fields["description"]="text";
			$this->nullable["description"]="NO";
			$this->default_value["description"]="";
			$this->fields["employee_ids"]="varchar(255)";
			$this->nullable["employee_ids"]="NO";
			$this->default_value["employee_ids"]="";
			$this->fields["table_id"]="int(11)";
			$this->nullable["table_id"]="NO";
			$this->default_value["table_id"]="";
			$this->fields["created_by"]="int(11)";
			$this->nullable["created_by"]="NO";
			$this->default_value["created_by"]="";
			$this->fields["created_at"]="datetime";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
		}
	}
?>