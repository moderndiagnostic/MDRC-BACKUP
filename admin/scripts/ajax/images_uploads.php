<?php
ini_set("display_errors",0);
ini_set("max_file_uploads",100);
$jsonclass = $app->load_module("Services_JSON");
$obj_JSON = new $jsonclass(SERVICES_JSON_LOOSE_TYPE);
$product_id=$app->getPostVar("product_id");
$fileCount = count($_FILES["file"]['name']);
echo $fileCount.'<br/><br/>';
	for($i=0; $i < $fileCount; $i++)
	{

		$image=$app->utility->FileUploadAjax([filename=>$_FILES['file']['name'][$i],filetmpname=>$_FILES['file']['tmp_name'][$i],folder=>"work_images"]);
		//$product_image=$app->utility->resize_single_image($_FILES['file']['name'][$i],$_FILES['file']['tmp_name'][$i],'../../../uploads/work_images/','1000');
		$m=$i+1;
		$update_field = array();
		$update_field['work_id'] = $product_id;
		$update_field['sort_id'] = $m;
		$update_field['image'] = $image;
		$update_field['status'] = 'Active';
		$obj_model_product_image = $app->load_model("work_images");
		$obj_model_product_image->map_fields($update_field);
		$ins_arr=$obj_model_product_image->execute("INSERT");
		$ins_arr[]=$ins;
	}
echo $obj_JSON->encode(array("RESULT"=>"OK","op"=>$op,"OPTION"=>array("id"=>$ins_arr,"product_images"=>$_FILES["file"]['name'])));
?>