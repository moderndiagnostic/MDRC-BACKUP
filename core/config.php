<?php
	/*===== Do PHP Version check. We need at least PHP 5.0.0 ========= */
	if (version_compare(PHP_VERSION, '5.0.0', '<'))
	{
		trigger_error("This system requires PHP 5.0.0 or above to work. <br/>You have PHP ".PHP_VERSION." in this system", E_USER_ERROR);
	}
	/*=================================================================*/
	define("__CONFIG__","1");
	/*================= Dont touch here ====================*/
	date_default_timezone_set("Asia/Kolkata");

	ini_set("date.timezone", "UTC");
	ini_set("display_errors", "off");
	error_reporting(E_ALL);
	/*=======================================================*/
	/*==================== Absolute path ====================*/
	//define("ABS_PATH","D:/wamp64/www/mdrcSales");
	define("ABS_PATH","/home/mdrcindia.com/html/SalesApp");
	/*=======================================================*/
	/*=============== Debug leve (1 to 4) ===================*/
	if(!defined("DEBUG"))
	{
		define("DEBUG",3);
	}
	define("DISPLAY_XPM4_ERRORS", true);
	if(!defined("ERROR_LOG"))
	{
		define("ERROR_LOG", ABS_PATH."logs/error_log.txt");
	}
	/*=======================================================*/
	/*======= Cache directory (to store cached files) =======*/
	if(!defined("CACHE_DIR"))
	{
		define("CACHE_DIR", ABS_PATH."cache");
	}
	/*=======================================================*/
	/*============= Cache time in seconds  ==================*/
	if(!defined("CACHE_TIME"))
	{
		define("CACHE_TIME", 60);
	}
	/*=======================================================*/
	/*================= DB Connection Info ==================*/
	define("DB_HOST","localhost");
	define("DB_DATABASE","mdrcindia_salesapp");
	define("DB_USERNAME","admin");
	define("DB_PASSWORD","Z1hR5GZ1dPFPeG83");
	/*=======================================================*/
	define("PROJECT_TILLE","MDRC Sales");
	define("FOOTER_COPY_RIGHT_PROJECT","www.mdrcindia.com");
	
	/*==========Default paramters for paging ================*/
	define("RECORD_PER_PAGE",15);
	define("SEGMENT_LENGTH",5);
	define("ALLVERSION","?v=1.10");
	define("p_name_limit",50);
	/*=======================================================*/

	/*============== Default Meta Tags ======================*/
	define("DEFAULT_TITLE","MDRC Sales");
	define("DEFAULT_KEYWORDS","MDRC Sales");
	define("DEFAULT_DESCRIPTION","MDRC Sales");
	/*=======================================================*/

	/*=============== Relative to ABS_PATH path ============*/
	if(!defined("VIR_DIR"))
	{
		define("VIR_DIR","");
	}
	/*=======================================================*/

	/*==== Access URL or Server Root of the application =====*/
	if ($_SERVER['HTTP_HOST'] === 'sales.mdrcindia.com') {
		define("SERVER_ROOT","https://".$_SERVER['SERVER_NAME'].'');
	} else {
		define("SERVER_ROOT","https://".$_SERVER['SERVER_NAME'].'/SalesApp');
	}
	/*=======================================================*/

	/*=== FTP Information - Needed for fileupload process ===*/
	if(!defined("USE_FTP"))
	{
		define("USE_FTP", false);
	}
	if(!defined("FTP_HOST"))
	{
		define("FTP_HOST", "localhost");
	}
	if(!defined("FTP_USERNAME"))
	{
		define("FTP_USERNAME", "");
	}
	if(!defined("FTP_PASSWORD"))
	{
		define("FTP_PASSWORD", "");
	}
	if(!defined("FTP_WWWDIR"))
	{
		define("FTP_WWWDIR", "");
	}
	/*=======================================================*/
	/*============== mail template storage path =============*/
	define("MAIL_TEMPLATE_PATH", "mail_templates");
	/*=======================================================*/
	/*== Automatically TRIP Post Variables in MySQL Query ===*/
	define("AUTO_TRIM", true);
	/*=======================================================*/
	/*================= Mail server settings ================*/
	if(!defined("SMTPDIRECT")){
		define("SMTPDIRECT", "0");
	}
	if(!defined("SMTPHOST")){
		define("SMTPHOST", "smtp-relay.brevo.com");
	}
	if(!defined("SMTPPORT")){
		define("SMTPPORT", "465");
	}
	//SMTP Connection encryption type. Possible values are: tls, ssl, sslv2 or sslv3
	if(!defined("SMTPSECURITY")){
			define("SMTPSECURITY", "ssl");
	}
	if(!defined("SMTPUSER")){
		define("SMTPUSER", "795eda002@smtp-brevo.com");
	}
	if(!defined("SMTPPASS")){
		define("SMTPPASS", "wyF2jKB1Jz7OCpTQ");
	}
	if(!defined("FROM_EMAIL")){
		define("FROM_EMAIL", "no-reply@mdrcindia.com");
	}
	if(!defined("FROM_NAME")){
		define("FROM_NAME", "MDRC India");
	}
	if(!defined("ENC_KEY")){
		define("ENC_KEY", "1234567890123456");
	}
	define("PAYU_SALT", "byHwqtYtwurlHbdWBDalT5eQMmyuSTDr");
	define("PAYU_MERCHANT_KEY", "qMCGD8");

	define("GOOGLE_MAP_API_KEY", "AIzaSyCMdIvv5Ajq0gtKqb7G3Yf-wHqsXZkq2rI");

	define("RAZOR_PAY_KEY", 'rzp_live_SXp7NrguqIFLLC');
	define("RAZOR_PAY_SECRET", "bACqM1TrjZpTx8xAOkcTiAMX");
	/*=======================================================*/
?>