<?php
	class model_employee_journey_finance_submanager{
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

			$this->fields["employee_journey_finance_manager_id"]="int(11)";
			$this->nullable["employee_journey_finance_manager_id"]="NO";
			$this->default_value["employee_journey_finance_manager_id"]="";

			$this->fields["employee_id"]="int(11)";
			$this->nullable["employee_id"]="NO";
			$this->default_value["employee_id"]="";
		}
	}
?>