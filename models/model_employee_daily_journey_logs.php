<?php
	class model_employee_daily_journey_logs{
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

			$this->fields["employee_daily_journey_id"]="int(11)";
			$this->nullable["employee_daily_journey_id"]="NO";
			$this->default_value["employee_daily_journey_id"]="";

			$this->fields["employee_id"]="int(11)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";

			$this->fields["title"]="varchar(255)";
			$this->nullable["title"]="NO";
			$this->default_value["title"]="";

			$this->fields["status"]="enum('Running','Pending','Approve By Manager','Approve By Finance','Reject By Manager','Reject By Finance')";
			$this->nullable["status"]="YES";
			$this->default_value["status"]="";
		}
	}
?>