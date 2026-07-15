<?
class userconfig
{
	var $config;
	/*==================================================================================*/
	/*  WRITE ALL USER CONFIG VARIABLE IN THIS FILE WHICH IS USED MORE THEN ONE TIME 	*/
	/*	FOR EXAMPLE , is given below , THIS VARIABLE IS DEFINED FOR UPLOADING FILE PATH */
	/*==================================================================================*/

	function __construct()
	{
		$this->config["city"] = "/uploads/city/";
		$this->config["employee"] = "/uploads/employee/";
		$this->config["client"] = "/uploads/client/";
		$this->config["taskUpdate"] = "/uploads/taskUpdate/";
		$this->config["punch"] = "/uploads/punch/";
		$this->config["daily_journey"] = "/uploads/daily_journey/";
		$this->config["clientFile"] = "/uploads/clientFile/";
		$this->config["samplePickup"] = "/uploads/samplePickup/";
	}
}
?>