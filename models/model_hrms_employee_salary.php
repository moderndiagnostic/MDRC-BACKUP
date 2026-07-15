<?php
	class model_hrms_employee_salary{
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
			$this->fields["hrms_employee_id"]="int(11)";
			$this->nullable["hrms_employee_id"]="NO";
			$this->default_value["hrms_employee_id"]="";
			$this->fields["salary_year"]="varchar(255)";
			$this->nullable["salary_year"]="NO";
			$this->default_value["salary_year"]="";
			$this->fields["salary_month"]="varchar(255)";
			$this->nullable["salary_month"]="NO";
			$this->default_value["salary_month"]="";
			$this->fields["salary_amount"]="float(9,2)";
			$this->nullable["salary_amount"]="NO";
			$this->default_value["salary_amount"]="";
			$this->fields["salary_gross_amount"]="float(9,2)";
			$this->nullable["salary_gross_amount"]="NO";
			$this->default_value["salary_gross_amount"]="";
			$this->fields["salary_td_amount"]="float(9,2)";
			$this->nullable["salary_td_amount"]="NO";
			$this->default_value["salary_td_amount"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="current_timestamp()";
		}
	}
?>