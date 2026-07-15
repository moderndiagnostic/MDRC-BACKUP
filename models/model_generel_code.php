<?php
	class model_generel_code{
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
			$this->fields["head_start_css"]="text";
			$this->nullable["head_start_css"]="NO";
			$this->default_value["head_start_css"]="";
			$this->fields["head_end_css"]="text";
			$this->nullable["head_end_css"]="NO";
			$this->default_value["head_end_css"]="";
			$this->fields["body_start_css"]="text";
			$this->nullable["body_start_css"]="NO";
			$this->default_value["body_start_css"]="";
			$this->fields["body_end_css"]="text";
			$this->nullable["body_end_css"]="NO";
			$this->default_value["body_end_css"]="";
			$this->fields["body_start_html"]="text";
			$this->nullable["body_start_html"]="NO";
			$this->default_value["body_start_html"]="";
			$this->fields["body_end_html"]="text";
			$this->nullable["body_end_html"]="NO";
			$this->default_value["body_end_html"]="";
			$this->fields["head_start_js"]="text";
			$this->nullable["head_start_js"]="NO";
			$this->default_value["head_start_js"]="";
			$this->fields["head_end_js"]="text";
			$this->nullable["head_end_js"]="NO";
			$this->default_value["head_end_js"]="";
			$this->fields["body_start_js"]="text";
			$this->nullable["body_start_js"]="NO";
			$this->default_value["body_start_js"]="";
			$this->fields["body_end_js"]="text";
			$this->nullable["body_end_js"]="NO";
			$this->default_value["body_end_js"]="";
			$this->fields["head_body"]="text";
			$this->nullable["head_body"]="NO";
			$this->default_value["head_body"]="";
		}
	}
?>