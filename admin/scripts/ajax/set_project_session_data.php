<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);

//get action

$p_cat_id=$app->getPostVar("p_cat_id");


if($p_cat_id>0)
{
	$_SESSION['search_p_cat_id']=$p_cat_id;
}
else
{
	$_SESSION['search_p_cat_id']='';	
}





		
echo $obj_json->encode(array("RESULT"=>"0","url"=>"","msg"=>"Success"));
?>