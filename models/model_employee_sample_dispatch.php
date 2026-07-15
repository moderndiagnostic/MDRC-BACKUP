<?php
	class model_employee_sample_dispatch{
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
			$this->fields["courier_type"]="varchar(255)";
			$this->nullable["courier_type"]="NO";
			$this->default_value["courier_type"]="";
			$this->fields["courier_name"]="varchar(255)";
			$this->nullable["courier_name"]="NO";
			$this->default_value["courier_name"]="";
			$this->fields["courier_person"]="varchar(255)";
			$this->nullable["courier_person"]="NO";
			$this->default_value["courier_person"]="";
			$this->fields["courier_mobile"]="varchar(255)";
			$this->nullable["courier_mobile"]="NO";
			$this->default_value["courier_mobile"]="";
			$this->fields["courier_delivery_date"]="varchar(255)";
			$this->nullable["courier_delivery_date"]="NO";
			$this->default_value["courier_delivery_date"]="";
			$this->fields["sample_count"]="int(11)";
			$this->nullable["sample_count"]="NO";
			$this->default_value["sample_count"]="";
			$this->fields["sent_center_id"]="int(11)";
			$this->nullable["sent_center_id"]="NO";
			$this->default_value["sent_center_id"]="";
			$this->fields["status"]="enum('Dispatched','Delivered','Trash')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Dispatched";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["employee_id"]="int(11)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
			$this->fields["receive_employee_id"]="int(11)";
			$this->nullable["receive_employee_id"]="NO";
			$this->default_value["receive_employee_id"]="";
			$this->fields["receive_created_at"]="timestamp";
			$this->nullable["receive_created_at"]="NO";
			$this->default_value["receive_created_at"]="0000-00-00 00:00:00";
		}
	}
?>