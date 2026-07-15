<?php
	class model_client_address{
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
			$this->fields["google_address"]="varchar(500)";
			$this->nullable["google_address"]="NO";
			$this->default_value["google_address"]="";
			$this->fields["google_latitude"]="varchar(255)";
			$this->nullable["google_latitude"]="NO";
			$this->default_value["google_latitude"]="";
			$this->fields["google_longitude"]="varchar(255)";
			$this->nullable["google_longitude"]="NO";
			$this->default_value["google_longitude"]="";
			$this->fields["google_pincode"]="varchar(255)";
			$this->nullable["google_pincode"]="NO";
			$this->default_value["google_pincode"]="";
			$this->fields["google_city"]="varchar(255)";
			$this->nullable["google_city"]="NO";
			$this->default_value["google_city"]="";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="0000-00-00 00:00:00";
		}
	}
?>