<?php
	class model_generel_settings{
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
			$this->fields["about"]="text";
			$this->nullable["about"]="NO";
			$this->default_value["about"]="";
			$this->fields["contact_number"]="varchar(50)";
			$this->nullable["contact_number"]="NO";
			$this->default_value["contact_number"]="";
			$this->fields["contact_number1"]="varchar(50)";
			$this->nullable["contact_number1"]="NO";
			$this->default_value["contact_number1"]="";
			$this->fields["contact_email"]="varchar(50)";
			$this->nullable["contact_email"]="NO";
			$this->default_value["contact_email"]="";
			$this->fields["contact_email1"]="varchar(50)";
			$this->nullable["contact_email1"]="NO";
			$this->default_value["contact_email1"]="";
			$this->fields["footer_text"]="text";
			$this->nullable["footer_text"]="NO";
			$this->default_value["footer_text"]="";
			$this->fields["address"]="text";
			$this->nullable["address"]="NO";
			$this->default_value["address"]="";
			$this->fields["logo_file"]="varchar(255)";
			$this->nullable["logo_file"]="NO";
			$this->default_value["logo_file"]="";
			$this->fields["to_emails"]="varchar(255)";
			$this->nullable["to_emails"]="NO";
			$this->default_value["to_emails"]="";
			$this->fields["cc_emails"]="text";
			$this->nullable["cc_emails"]="NO";
			$this->default_value["cc_emails"]="";
			$this->fields["image_alt"]="varchar(255)";
			$this->nullable["image_alt"]="NO";
			$this->default_value["image_alt"]="";

			$this->fields["finance_manager_id"]="id(11)";
			$this->nullable["finance_manager_id"]="NO";
			$this->default_value["finance_manager_id"]="";
		}
	}
?>