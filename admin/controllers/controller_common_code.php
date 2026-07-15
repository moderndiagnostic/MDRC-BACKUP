<?
	class _common_code extends controller{
		
		function init()
		{
		}
		
		function onload(){
			
			
			$this->assign("manage_for", " Head/Body Code");
			$this->assign("to_do", "");
			
			$obj_model_generel_settings = $this->app->load_model("generel_code");
			$rs = $obj_model_generel_settings->execute("SELECT", false,"","id=1");
			
			$this->app->assign("rs_data",$rs[0]);
			$this->app->assign_form_data("frm_generel_settings",$rs[0]);
			
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
			$obj_model_generel_settings = $this->app->load_model("generel_code");
			$obj_model_generel_settings->map_fields($update_field);
			$update_id = $obj_model_generel_settings->execute("UPDATE", false, "", "id=1");		
			if($update_id!=NULL)
			{
				$this->app->utility->set_message("Updated successfully.", "SUCCESS");
				$this->app->redirect("index.php?view=common_code");
			}
			else
			{
				$this->app->utility->set_message("Record not updated...", "ERROR");
				$this->app->redirect("index.php?view=common_code");
			}
		}	
		
	}	
?>