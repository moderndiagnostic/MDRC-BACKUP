<?
class _client_addedit extends controller
{

	function init()
	{
	}

	function onload()
	{
		$id=$this->app->getGetVar('id');
		if($id!='')
		{
			$this->app->assign("manage_for","Edit");
			$this->load_data();
		}
		else
		{
			$this->app->assign("manage_for","Add");
		}
		$this->app->assign("to_do","Client");
	}
	
	function load_data()
	{
		$id=$this->app->getGetVar('id');
		$obj_model_product = $this->app->load_model("client", $id);
		$rscat = $obj_model_product->execute("SELECT");
		if(count($rscat)>0)
		{
			$this->app->assign_form_data("client_form", $rscat[0]);
			$this->app->assign("rscat", $rscat[0]);
		}
		else
		{
			$this->app->redirect("index.php?view=client_list");
		}
	}

	
	function update_data()
	{
		$title=$this->app->getPostVar('title');		
		$id=$this->app->getPostVar('id');		
		if($id!="")
		{
			$obj_model_record = $this->app->load_model("client");
			$result=$obj_model_record->execute("SELECT",false,"","id='".$id."'");

			$update_field = array();
			
			if(!empty($_FILES['image']['name']))
			{
				@unlink('../'.$this->app->get_user_config("client").'/'.$result[0]["image"]);
				$image=$this->app->utility->FileUpload([filename=>$_FILES['image']['name'],filetmpname=>$_FILES['image']['tmp_name'],folder=>"client"]);
				$update_field["image"] = $image;
			}
			$update_field['title'] = $title;
			
			$obj_model_product = $this->app->load_model("client", $id);
			$obj_model_product->map_fields($update_field);
			if($obj_model_product->execute("UPDATE")>0)
			{	

				$this->app->utility->set_message("Record updated successfully", "SUCCESS");
				$this->app->redirect("index.php?view=client_list");
			}
			else
			{
				$this->app->utility->set_message("Ooops... There was a problem in update records", "ERROR");
				$this->app->redirect("index.php?view=client_list");
			}
		}
		else
		{
			//INSERT RECORDS
			$update_field = array();
			$update_field['title'] = $title;
			if(!empty($_FILES['image']['name']))
			{
				$image=$this->app->utility->FileUpload([filename=>$_FILES['image']['name'],filetmpname=>$_FILES['image']['tmp_name'],folder=>"client"]);
				$update_field["image"] = $image;
			}
			$obj_model_product = $this->app->load_model("client");
			$obj_model_product->map_fields($update_field);
			$ins=$obj_model_product->execute("INSERT");
			if($ins>0)
			{
				$this->app->utility->set_message("Record added successfully", "SUCCESS");
				$this->app->redirect("index.php?view=client_list");
			}
			else
			{
				$this->app->utility->set_message("Try Again.", "ERROR");
				$this->app->redirect("index.php?view=client_list");
			}
		}
	}
}
?>