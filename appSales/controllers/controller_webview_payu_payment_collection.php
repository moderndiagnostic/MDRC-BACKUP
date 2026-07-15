<?
class _webview_payu_payment_collection extends controller {
	function init(){
	}
	function onload()
	{
		
		$id=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getGetVar('id'));
		
		if($id!=''){
		
			$obj_model_sample_pickup = $this->app->load_model("employee_sample_pickup_payment");
			$obj_model_sample_pickup->join_table("client", "left", array(), array("client_id"=>"id"));
			$sample_pickup_payment=$obj_model_sample_pickup->execute("SELECT",false,"","employee_sample_pickup_payment.id='".$id."'","employee_sample_pickup_payment.id desc");
			
			if($sample_pickup_payment[0]['payment_status']=='Fail')
			{
				$MERCHANT_KEY=PAYU_MERCHANT_KEY;
				$SALT=PAYU_SALT;

				$txnId = $sample_pickup_payment[0]['transaction_id'];
				$amount = $sample_pickup_payment[0]['amount'];
				//$amount = 1.00;
				$productInfo = 'Sample Pickup Payment';
				$firstName=$sample_pickup_payment[0]['client_company_name'];
				$email = !empty($sample_pickup_payment[0]['client_phone'])?$sample_pickup_payment[0]['client_phone']:'info@mdrc@gmail.com';
				$phone = $sample_pickup_payment[0]['client_mobile'];
				$sUrl=SERVER_ROOT.'/appSales/index.php?view=webview_payu_payment_success';
				$fUrl=SERVER_ROOT.'/appSales/index.php?view=webview_payu_payment_success';

				// Generate hash
				$hash_string = $MERCHANT_KEY . '|' . $txnId . '|' . $amount . '|' . $productInfo . '|' . $firstName . '|' . $email . '|'.$id.'||||||||||' . $SALT;
				$hash = strtolower(hash('sha512', $hash_string));

				// Build full payment URL
				$paymentParams = [
					'key' => $MERCHANT_KEY,
					'txnid' => $txnId,
					'amount' => $amount,
					'productinfo' => $productInfo,
					'firstname' => $firstName,
					'email' => $email,
					'phone' => $phone,
					'surl' => $sUrl,
					'furl' => $fUrl,
					'pg'   =>'QR',
					'bankcode'=>'UPIQR',
					's2s_client_ip'=>$_SERVER['REMOTE_ADDR'],
					's2s_device_info'=>'Chrome',
					'txn_s2s_flow'=>4,
					'udf1'=>$id,
					'hash' => $hash
				];

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,'https://secure.payu.in/_payment');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					"accept: text/plain",
					"content-type: application/x-www-form-urlencoded"
				]);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paymentParams));

				$curl_response = curl_exec($ch);
				$response=json_decode($curl_response,true);
				
				if(isset($response['result']['acsTemplate'])){
					$base64=base64_decode($response['result']['acsTemplate']);
					echo $base64;exit;
				} else {
					$this->app->redirect("index.php?view=webview_blank&webview=close");
					exit;
				}
			}
			else
			{
				$this->app->redirect("index.php?view=webview_blank&webview=close");
				exit;
			}
		}
		else
		{
			$this->app->redirect("index.php?view=webview_blank&webview=close");
			exit;
		}
	}
}
?>