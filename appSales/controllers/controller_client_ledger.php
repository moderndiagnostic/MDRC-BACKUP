<?
class _client_ledger extends controller{
	function init(){
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);

		if($employeeID!='' && $clientID!='')
		{
			$obj_model_client = $this->app->load_model("client");
			$clientDetail = $obj_model_client->execute("SELECT",false,"","id='".$clientID."'","client.id desc limit 0,1");
			$client=$clientDetail[0];
			$panelId=$client['panel_id'];

			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://182.156.200.228/mdrcnew/api/BookingAPI/GetLedger',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{"PanelID":"'.$panelId.'"}',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
			));

			$response = curl_exec($curl);
			
			curl_close($curl);
			$res=json_decode($response,true);

			if(!empty($res)){
				$balanceTitle='This Month Opening Balance';
				$balance=$res['data'][0]['OpeningBalance'];

				$result=[
					"balanceTitle"=>$balanceTitle,
					"balance"=>$balance
				];
				$message=array("message"=>"Date fetch successfully.","msgCode"=>"1","result"=>$result);
			} else {
				$message=array("message"=>"Data not found.","msgCode"=>"0");
			}
		}
		else
		{
			$message=array("message"=>"Data is Missing.","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>