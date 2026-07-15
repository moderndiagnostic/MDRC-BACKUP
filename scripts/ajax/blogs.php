<?php
mysqli_set_charset($app->set_db_conn(),'utf-8');

//include('db.php');

$limit = 4;

$actionType = $app->getPostVar('actionType');





if($actionType=='list')
{
	

$page_no=$app->getPostVar("page");

$cat=$app->getPostVar("cat");

$subcat=$app->getPostVar("subcat");
$subsubcat=$app->getPostVar("subsubcat");



$price_v=$app->getPostVar("price_v");

$size_v	=$app->getPostVar("size_v");

$style_v=$app->getPostVar("style_v");

$brand_v=$app->getPostVar("brand_v");

$serach_keyword=$app->getPostVar("serach_keyword");
$serach_cat=$app->getPostVar("serach_cat");
$product_new=$app->getPostVar("product_new");



$order_v=$app->getPostVar("order_v");

$total_products=$app->getPostVar("total_products");
  

$data=array("page"=>$page_no); 

echo $app->utility->load_blogs($data,$limit,$cat,$subcat,$subsubcat,$size_v,$style_v,$brand_v,$price_v,$serach_keyword,$serach_cat,$product_new,$order_v,$total_products);


}










?>