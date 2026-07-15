<?php
	class model_client_bank{
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
			$this->fields["client_id"]="bigint(20)";
			$this->nullable["client_id"]="NO";
			$this->default_value["client_id"]="";
			$this->fields["account_holder_name"]="varchar(255)";
			$this->nullable["account_holder_name"]="NO";
			$this->default_value["account_holder_name"]="";
			$this->fields["bank_name"]="varchar(255)";
			$this->nullable["bank_name"]="NO";
			$this->default_value["bank_name"]="";
			$this->fields["account_no"]="varchar(255)";
			$this->nullable["account_no"]="NO";
			$this->default_value["account_no"]="";
			$this->fields["ifsc_code"]="varchar(255)";
			$this->nullable["ifsc_code"]="NO";
			$this->default_value["ifsc_code"]="";
			$this->fields["bank_address"]="varchar(255)";
			$this->nullable["bank_address"]="NO";
			$this->default_value["bank_address"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
		}
	}
?>