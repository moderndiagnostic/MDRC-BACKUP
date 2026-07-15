<?
	class _account_setting extends controller{
		function init(){
			//$this->app->enable_cache("home.html");
		}
		function onload(){
			$this->assign("manage_for", "Account Setting");
			$this->assign("to_do", "");
			$obj_model_admin = $this->app->load_model("employee",$_SESSION['employeeId']);
			$rs = $obj_model_admin->execute("SELECT");
			$this->app->assign_form_data("frm_profile",$rs[0]);
			$this->app->assign("rscat",$rs[0]);
			$data = $this->app->compile();
			$this->load_parser($data);
			$this->parser->assign("MESSAGE", $this->app->utility->get_message());
			$this->parser->parse('main');
			$this->update_ouput($this->parser->text('main'));
			$this->unload_parser();
		}
		function update_data(){
				$login_password=$this->app->getPostVar("login_password");
				$confirm_password=$this->app->getPostVar("confirm_password");
				$update_field = array();
				if($login_password==$confirm_password)
				{
					$obj_model_admin = $this->app->load_model("employee");
					$obj_model_admin->map_fields($update_field);
					$update_id = $obj_model_admin->execute("UPDATE", false, "", "id=".$_SESSION['employeeId']);
					if($update_id!=NULL)
					{
						$this->app->utility->set_message("Admin record updated successfull...", "SUCCESS");
						$this->app->redirect("index.php?view=account_setting");
					}
					else
					{
						$this->app->utility->set_message("Record not updated...", "ERROR");
						$this->app->redirect("index.php?view=account_setting");
					}
				}
				else
				{
					$this->app->utility->set_message("Password Not Matched.", "ERROR");
					$this->app->redirect("index.php?view=account_setting");
				}
			
		}
	}
?>