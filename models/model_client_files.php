<?php
	class model_client_files{
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
			$this->fields["company_photo"]="varchar(255)";
			$this->nullable["company_photo"]="NO";
			$this->default_value["company_photo"]="";
			$this->fields["aadhar_card_no"]="varchar(255)";
			$this->nullable["aadhar_card_no"]="NO";
			$this->default_value["aadhar_card_no"]="";
			$this->fields["aadhar_card_front_photo"]="varchar(255)";
			$this->nullable["aadhar_card_front_photo"]="NO";
			$this->default_value["aadhar_card_front_photo"]="";
			$this->fields["aadhar_card_back_photo"]="varchar(255)";
			$this->nullable["aadhar_card_back_photo"]="NO";
			$this->default_value["aadhar_card_back_photo"]="";
			$this->fields["pancard_photo"]="varchar(255)";
			$this->nullable["pancard_photo"]="NO";
			$this->default_value["pancard_photo"]="";
			$this->fields["pancard_no"]="varchar(255)";
			$this->nullable["pancard_no"]="NO";
			$this->default_value["pancard_no"]="";
			$this->fields["incorporation_photo"]="varchar(255)";
			$this->nullable["incorporation_photo"]="NO";
			$this->default_value["incorporation_photo"]="";
			$this->fields["registration_photo"]="varchar(255)";
			$this->nullable["registration_photo"]="NO";
			$this->default_value["registration_photo"]="";
			$this->fields["gst_no"]="varchar(255)";
			$this->nullable["gst_no"]="NO";
			$this->default_value["gst_no"]="";
			$this->fields["gst_photo"]="varchar(255)";
			$this->nullable["gst_photo"]="NO";
			$this->default_value["gst_photo"]="";
			$this->fields["sign_photo"]="varchar(255)";
			$this->nullable["sign_photo"]="NO";
			$this->default_value["sign_photo"]="";
			$this->fields["created_at"]="timestamp";
			$this->nullable["created_at"]="NO";
			$this->default_value["created_at"]="current_timestamp()";
			$this->fields["updated_at"]="timestamp";
			$this->nullable["updated_at"]="NO";
			$this->default_value["updated_at"]="0000-00-00 00:00:00";
		}
	}
?>