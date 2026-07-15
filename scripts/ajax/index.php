<?
	define("VIR_DIR","scripts/ajax/");
	include("../../core/app.php");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	$app = & app::get_instance();
	$app->initialize();

	if($app->domSecure->isReferrerSecured){
		$method = $app->getPostVar("method");
		if($method!=""){
			if(file_exists($method.".php")){
				include($method.".php");
			}
		}
	}
	
	$app->unload();
?>