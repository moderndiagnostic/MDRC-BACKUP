<?
	class global_include
	{
		private $settings = array();
		private $app;
		private $initialized = false;
		private $system_acl_permission = array();
		public function __construct()
		{
			$this->app = &app::get_instance();
		}
		public function initalize()
		{
			if(!$this->initialized)
			{
				$this->initialized=true;
				mysqli_set_charset($this->app->set_db_conn(),'utf-8');
				
				if(VIR_DIR=="admin/")
				{
					$this->app->setTitle(DEFAULT_TITLE." - Administrator");
					if($this->app->getCurrentView()!="forgot_password")
					{
						if($this->app->getCurrentView()!="default" && (empty($_SESSION["employeeId"])))
						{
							$this->app->redirect($this->app->root_relative."admin/index.php");
						}
						else if($this->app->getCurrentView()=="default" && (isset($_SESSION["employeeId"])) && $this->app->getCurrentAction()!="do_logout")
						{
							$this->app->redirect($this->app->root_relative."admin/index.php?view=home");
						}
						else
						{
							if(empty($_SESSION['records']))
							{
								$_SESSION['records'] = ($this->app->getPostVar("record_per_page")==NULL?10:$this->app->getPostVar("record_per_page"));
							}
							else
							{
								if($this->app->getPostVar("record_per_page") != NULL)
								{
									$_SESSION['records'] = $this->app->getPostVar("record_per_page");
								}
						}
						$rs  = array();
						$val = 5;
						for($i=0;$i<10;$i++)
						{
							$rs[$val] = $val;
							$val = $val+5;
						}
						$this->app->assign("record", $rs);
						$this->app->assign("field_record_per_page", $_SESSION['records']);
					 }
					}

					$new_page=$this->app->getCurrentView();
					
					// if($_SESSION["employeeRole"]!='Admin' && !in_array($new_page,['employee_dashboard','account_setting','employee_activity']))
					// {
					// 	$this->app->redirect($this->app->root_relative."admin/index.php?view=employee_dashboard");
					// }
					if($_SESSION['search_by']!='' && $_SESSION['search_keyword']!='' && $_SESSION['current_page']=='')
					{
								$_SESSION['current_page']=$this->app->getCurrentView();
					}
					if($_SESSION['current_page']!=$new_page)
					{
						$_SESSION['current_page']='';
						$_SESSION['search_start_date']='';
						$_SESSION['search_end_date']='';
						$_SESSION['search_category']='';
						$_SESSION['search_type']='';
					}
					$this_page=$this->app->getCurrentView();
					
					$obj_model_admin = $this->app->load_model("employee");
					$rs_admin = $obj_model_admin->execute("SELECT", false, "", "id='".$_SESSION['employeeId']."'");
					$this->app->assign("rs_admin",$rs_admin[0]);
					
					if($_SESSION['employeeId']!='1' && $this->app->getCurrentView()=="account_list")
					{
						$this->app->redirect($this->app->root_relative."admin/index.php?view=home");
					}

					if($this->app->getCurrentView()=="project_list" || $this->app->getCurrentView()=="project_addedit" || $this->app->getCurrentView()=="projects_gallery_list")
					{
					}
					else
					{
						$_SESSION['search_p_cat_id']='';
					}

					$obj_model_generel_settings= $this->app->load_model("generel_settings");
					$rs_gs = $obj_model_generel_settings->execute("SELECT", false, "", "");
					$this->app->assign("gs",$rs_gs[0]);
				}
				
				
				if(VIR_DIR=="")
				{
					
			  	}
			}
		}
	}
		/*==================================================================================*/
		/*	DEFINE ALL GLOBAL FUNCTIONS HERE												*/
		/*==================================================================================*/
?>