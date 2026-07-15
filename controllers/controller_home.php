<?

class _home extends controller{

	function init(){

	}

	function onload()
	{
		$this->app->redirect('https://sales.mdrcindia.com/admin');
		/* $curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://182.156.200.228/mdrcnew/api/BookingAPI/GetEmployee',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>'{"EmployeeID":""}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		echo $response; exit; */

	}
}
?>