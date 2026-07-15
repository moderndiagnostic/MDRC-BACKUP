<?
	class _about_page extends controller
	{
		function init()
		{	
		}
		
		function onload()
		{		
			$obj_model_generel_settings = $this->app->load_model("about_detail");
			$rs = $obj_model_generel_settings->execute("SELECT", true);
			
			$this->app->assign("rscat",$rs[0]);
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
			$obj_model_record = $this->app->load_model("about_detail");
			$result=$obj_model_record->execute("SELECT", true);
			

			$update_field = array();

			if(!empty($_FILES['about_image']['name']))
			{
				$image=$this->app->utility->FileUpload([filename=>$_FILES['about_image']['name'],filetmpname=>$_FILES['about_image']['tmp_name'],folder=>"about"]);
				$update_field["image"] = $image;
			}
			
			$obj_model_generel_settings = $this->app->load_model("about_detail");
			$obj_model_generel_settings->map_fields($update_field);
			$update_id = $obj_model_generel_settings->execute("UPDATE", false, "", "id=1");		
			if($update_id!=NULL)
			{
				$this->app->utility->set_message("About Content updated successfull...", "SUCCESS");
				$this->app->redirect("index.php?view=about_page");
			}
			else
			{
				$this->app->utility->set_message("Record not updated...", "ERROR");
				$this->app->redirect("index.php?view=about_page");
			}
		}	
		
	}	
?>