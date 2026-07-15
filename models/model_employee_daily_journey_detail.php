<?php
	class model_employee_daily_journey_detail{
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

			$this->fields["manager_employee_id"]="int(11)";
			$this->nullable["manager_employee_id"]="NO";
			$this->default_value["manager_employee_id"]="";

			$this->fields["manager_datetime"]="datetime";
			$this->nullable["manager_datetime"]="Yes";
			$this->default_value["manager_datetime"]="";

			$this->fields["manager_remark"]="text";
			$this->nullable["manager_remark"]="NO";
			$this->default_value["manager_remark"]="";

			$this->fields["finance_employee_id"]="int(11)";
			$this->nullable["finance_employee_id"]="NO";
			$this->default_value["finance_employee_id"]="";

			$this->fields["finance_datetime"]="datetime";
			$this->nullable["finance_datetime"]="Yes";
			$this->default_value["finance_datetime"]="";

			$this->fields["finance_remark"]="text";
			$this->nullable["finance_remark"]="NO";
			$this->default_value["finance_remark"]="";
		}
	}
?>