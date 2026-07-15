<?
class _webview_payu_payment_success extends controller {
	function init(){
	}
	function onload()
	{
		//$response=$this->app->getPostVars();
		//file_put_contents('webhook_log2.txt', print_r($response, true), FILE_APPEND);
		$id=$this->app->getPostVar('udf1');
		$status=$this->app->getPostVar('status');
		$amount=$this->app->getPostVar('amount');
		$txnid = $this->app->getPostVar('txnid');

		$obj_model_employee = $this->app->load_model("employee_sample_pickup_payment");
		$obj_model_employee->join_table("client", "left", array(), array("client_id"=>"id"));
		$paymentData=$obj_model_employee->execute("SELECT",false,"","employee_sample_pickup_payment.id='".$id."'");
	
		if ($status=='success' && count($paymentData)>0) {
			
			$formData='Panel_id='.$paymentData[0]['client_panel_id'].'&Bank=Online&Remarks='.($paymentData[0]['remark']??'.').'&ReceivedAmt='.$paymentData[0]['amount'].'&OrderId='.$id;
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://lis6.mdrcindia.com/mdrcnew/api/BookingInvoiceAcount/BookingAccountAPI',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>$formData,
			));
			$response = curl_exec($curl);
			curl_close($curl);
			$responseArray=json_decode($response,true);
			//file_put_contents('webhook_log_lis.txt', print_r($responseArray, true), FILE_APPEND);
			//echo $response;

			$data_t=array();	
			$data_t['payment_status']='Success';
			$data_t['transaction_id']=$txnid;
			$data_t['lis_transaction_id']=$responseArray['VerifyID']??'';
			$data_t['transaction_date']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_payment");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$id."'");

			$data_t=array();	
			$data_t['employee_sample_pickup_payment_id']=$id;
			$data_t['request_json']=$formData??'';
			$data_t['response_json']=$response??'';
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_payment_lis_calls");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("INSERT",false,"","");

			$this->app->redirect("index.php?view=webview_blank&payment_status=success&id=".$id);
		}
		else
		{

			$data_t=array();	
			$data_t['payment_status']='Fail';
			$data_t['transaction_id']=$txnid;
			$data_t['lis_transaction_id']=NULL;
			$data_t['transaction_date']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_payment");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$id."'");

			$this->app->redirect("index.php?view=webview_blank&payment_status=fail&id=".$id);
		}
		
	}

	
}
?>