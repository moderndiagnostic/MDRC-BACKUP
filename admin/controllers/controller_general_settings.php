<?
	class _general_settings extends controller{
		
		function init(){
			//$this->app->enable_cache("home.html");
		}
		
		function onload(){
			
			$obj_employee=$this->app->load_model("employee");
			$rs_employee = $obj_employee->execute("SELECT", false, "", "status='Active' and employee_role='Employee'");	

			$employee[]="Select";
			foreach($rs_employee as $item){
			$employee[$item['id']]=$item["name"];
			}
			$this->app->assign("employee",$employee);
			
			$this->assign("manage_for", "General Settings");
			$this->assign("to_do", "");
			
			$obj_model_generel_settings = $this->app->load_model("generel_settings");
			$rs = $obj_model_generel_settings->execute("SELECT", true);
			
			$this->app->assign("rs_data",$rs[0]);
			$this->app->assign_form_data("frm_generel_settings",$rs[0]);
			
			$p_type='';
			$this->app->assign("p_type",$p_type);
			
			$data = $this->app->compile();
			$this->load_parser($data);
			$this->parser->assign("MESSAGE", $this->app->utility->get_message());
			$this->parser->parse('main');			
			$this->update_ouput($this->parser->text('main'));
			$this->unload_parser();
			
		}
		
		
		function update_data()
		{
				
			$update_field = array();


			if(!empty($_FILES['logo_file']['name']))
			{
				$logo_image=$this->app->utility->FileUpload([filename=>$_FILES['logo_file']['name'],filetmpname=>$_FILES['logo_file']['tmp_name'],folder=>"logo"]);
				$update_field["logo_file"] = $logo_image;
			}

			$obj_model_generel_settings = $this->app->load_model("generel_settings");
			$obj_model_generel_settings->map_fields($update_field);
			$update_id = $obj_model_generel_settings->execute("UPDATE", false, "", "id=1");		
			if($update_id!=NULL)
			{
				$this->app->utility->set_message("Generel Settings updated successfully.", "SUCCESS");
				$this->app->redirect("index.php?view=general_settings");
			}
			else
			{
				$this->app->utility->set_message("Record not updated...", "ERROR");
				$this->app->redirect("index.php?view=general_settings");
			}
		}	
		
	}	
?>