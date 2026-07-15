<?php
	class model_employee_daily_journey{
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

			$this->fields["journey_date"]="date";
			$this->nullable["journey_date"]="NO";
			$this->default_value["journey_date"]="";

			$this->fields["start_datetime"]="datetime";
			$this->nullable["start_datetime"]="NO";
			$this->default_value["start_datetime"]="";

			$this->fields["start_km"]="varchar(255)";
			$this->nullable["start_km"]="NO";
			$this->default_value["start_km"]="";

			$this->fields["start_image"]="varchar(255)";
			$this->nullable["start_image"]="NO";
			$this->default_value["start_image"]="";

			$this->fields["start_latitude"]="varchar(255)";
			$this->nullable["start_latitude"]="NO";
			$this->default_value["start_latitude"]="";

			$this->fields["start_longitude"]="varchar(255)";
			$this->nullable["start_longitude"]="NO";
			$this->default_value["start_longitude"]="";

			$this->fields["image_path"]="varchar(255)";
			$this->nullable["image_path"]="NO";
			$this->default_value["image_path"]="";

			$this->fields["end_datetime"]="datetime";
			$this->nullable["end_datetime"]="Yes";
			$this->default_value["end_datetime"]="";

			$this->fields["end_km"]="varchar(255) ";
			$this->nullable["end_km"]="NO";
			$this->default_value["end_km"]="";

			$this->fields["end_image"]="varchar(255) ";
			$this->nullable["end_image"]="NO";
			$this->default_value["end_image"]="";

			$this->fields["end_latitude"]="varchar(255) ";
			$this->nullable["end_latitude"]="NO";
			$this->default_value["end_latitude"]="";

			$this->fields["end_longitude"]="varchar(255) ";
			$this->nullable["end_longitude"]="NO";
			$this->default_value["end_longitude"]="";

			$this->fields["total_km"]="varchar(255) ";
			$this->nullable["total_km"]="NO";
			$this->default_value["total_km"]="";

			$this->fields["status"]="enum('Running','Pending','Approve By Manager','Approve By Finance','Reject By Manager','Reject By Finance')";
			$this->nullable["status"]="NO";
			$this->default_value["status"]="Running";
		}
	}
?>