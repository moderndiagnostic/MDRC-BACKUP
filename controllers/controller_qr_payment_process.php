<?
class _qr_payment_process extends controller{

	function init()
	{
	}
	
	function onload()
	{
		define("CCA_WORKING_KEY", "636CC2C22573FA93F70D751324881CB1");
		//print_r($this->app->getPostVar('encResp')); exit; 
		/* $encResponse='a56bd753aae92a087087386ea587e9bfc8e73e8ecb5da1a88c90f5b7d82dc3a18df1251859f463a00cd9f5842406d9b5eccdf91aec558e85b8b42feaed9295d7761f4195364df1387b107e57a1ab2e834c5d23ee19ae96f5708f71c8232945a9011b3c70bd5231bc9d00ea55aea05457dcd6922dff75a10e14ed3341e56a42a9864a124a65278d6ed2947e6c1f608d7d08ecbb016765c1296f0e88f27ad5a492ce16dfe02f85eee81c53e8f6b296846cb13f9908f1ab770f8bbd132e35acf3d649806566dc3c2b41aca7b12916519558d057dd63488f300defd3840937092047c5d77c2f4a3ca944b8ac1b8a4c71a9b8b14da5e06a3d66a2f88ac6525149f1faf69c6d700b359962bbbf7001b63ab894ecee6d577b4a8ed01d4ab52a5132d703e7ca4612c3ee6eaab211e803babb1defa7318bfe9c22f4ec2c392dc701811c20f605ede7b155125177ec69a9aa5fa602423f42f9a696313be0606bb635bb9755becab9285926777c7319cd25121c47c7f3a475b40366af8ee8936713ebf8e25d2600ed261c8ca7d9b47ba768a2708722cca9b3549b1f0dd6d4f683c3edc173628d4d001e09c92e5d42255269e4e60da98f5ffed9154a055875e9611c3d853f7d2d88e332b7f3b76a9d125dcfb6aded5b4ec6a5bc83b03c4e581948e985b64306c05da8b63db38ce2b04fe564be0e86fe22968003b976b2cd97c75249f35b9d698e158f29d965115cf18047b38e5c761f2f84d25056c89e612efef4b4e49746f13b1c76c4684302b539f98b8d88d3ef364b591506febdcc2a394b4dd274be41b906d4249b015d1f95a2e732b647f0b23cc62e5cb4682b892cb998147cc33330331c5e13a792e0a27da08edc0ab4c245d2f60138cabce5bebcd0a1c3edf87c45e6104a79dc4629d9ad33aaef15e5e87bc123ff65a40cffb9aa1e65bac64a227a3da39a96c2c4fca245ad0c6fc1d63562ad1960097ce1a6d4571abfb2b94ef0619a6037fd3eaadec766413d237f95f09bbf0ca9a389fa129d0044f02506f6496e22766c0ae71913fd270a2e65db0b6a714c1eaba1cd2cc0a9c6bf30f5e752f3ea11'; */
		$encResponse=$this->app->getPostVar('encResp');

		include('../ccavenue/Crypto.php');
		$workingKey=CCA_WORKING_KEY;       //Working Key should be provided here.
		$rcvdString=decrypt($encResponse,$workingKey);    //Crypto Decryption used as per the specified working key.
		$decryptValues=explode('&', $rcvdString);
		$dataSize = sizeof ($decryptValues);
		for($i = 0; $i < $dataSize; $i ++) {
			$information = explode ( '=', $decryptValues [$i] );
			$responseMap [$information [0]] = $information [1];
		}
	
		$order_payment_id=$responseMap['order_id'];
		$order_payment_id=substr($order_payment_id,7);
		$tracking_id=$responseMap['tracking_id'];
		$gateway_order_status=$responseMap['order_status'];

		if(!empty($order_payment_id) && is_numeric($order_payment_id))
		{
			$obj_model_payment_data=$this->app->load_model("payment_link_transaction");
			$obj_model_payment_data->join_table("payment_links", "left", array(), array("payment_link_id"=>"id"));
			$result_pay=$obj_model_payment_data->execute("SELECT",false,"","payment_link_transaction.id='".$order_payment_id."'");
			if(count($result_pay)>0) {

				$order_id=$result_pay[0]['id'];
				$_SESSION['DirectOrderID']=$order_id;

					
				//first check payment id status
				if($result_pay[0]['status']=='Success')
				{

					/*------------------Mail Data Array------------------*/
					$send_data_arary=['name'=>$result_pay[0]['payment_links_name'],'email'=>$result_pay[0]['payment_links_email'],'mobile'=>$result_pay[0]['payment_links_mobile'],'amount'=>$result_pay[0]['payment_links_amount'],'message'=>$result_pay[0]['remark'],'order_pay_status'=>$result_pay[0]['status'],'payment_id'=>$result_pay[0]['transaction_id'],'date'=>$result_pay[0]['created_at']];
					/*------------------Mail Data Array------------------*/

					/*------------------User Start for mail function------------------*/
					$template_name='direct_payment_order_user';
					$subject='Thank you for Payment-Format for Payment done';
					$mail_for='User';
					$to_mail=$result_pay[0]['payment_links_email'];
					$data=['template_name'=>$template_name,'send_data_arary'=>$send_data_arary,'subject'=>$subject,'mail_for'=>$mail_for,'to_mail'=>$to_mail];
					$this->app->utility->sendMial($data);
					/*------------------User End for mail function------------------*/
				
					/*------------------Admin Start for mail function------------------*/
					$template_name='direct_payment_order_admin';
					$subject='Online payment From '.$result_pay[0]['payment_links_name'].' on Website';
					$mail_for='Admin';
					$data=['template_name'=>$template_name,'send_data_arary'=>$send_data_arary,'subject'=>$subject,'mail_for'=>$mail_for];
					$this->app->utility->sendMial($data);
					/*------------------Admin End for mail function------------------*/


					//if payment already success, redirect to payment success
					$this->app->redirect('qr-payment-success/'.$order_id.'');
				}

				//update order payment data entry
				$payment_data=[];
				
				//if payment succss only
				if($gateway_order_status=='Success') { $payment_data['status']='Success'; }
		
				$payment_data['transaction_id']=$tracking_id;
				$payment_data['created_at']=date('Y-m-d H:i:s');
				$obj_model_payment_data=$this->app->load_model("payment_link_transaction");
				$obj_model_payment_data->map_fields($payment_data);
				$obj_model_payment_data->execute("UPDATE",false,"","id='".$order_payment_id."'");

				if($gateway_order_status=='Success')
				{
					$data=[];
					$data['success_payment_link_transaction_id']=$order_id;
					$data['status']='Success';
					$obj_payment_data=$this->app->load_model("payment_links");
					$obj_payment_data->map_fields($data);
					$obj_payment_data->execute("UPDATE",false,"","id='".$result_pay[0]['payment_link_id']."'");
				}
				else
				{
					$data=[];
					$data['status']='Fail';
					$obj_payment_data=$this->app->load_model("payment_links");
					$obj_payment_data->map_fields($data);
					$obj_payment_data->execute("UPDATE",false,"","id='".$result_pay[0]['payment_link_id']."'");
				}
				//update order status to confirm
				if($gateway_order_status=='Success' && $order_id>0) 
				{ 
					$obj_model_payment_data2=$this->app->load_model("payment_link_transaction");
					$obj_model_payment_data2->join_table("payment_links", "left", array(), array("payment_link_id"=>"id"));
					$result_pay=$obj_model_payment_data2->execute("SELECT",false,"","payment_link_transaction.id='".$order_id."'");

					/*------------------Mail Data Array------------------*/
					$send_data_arary=['name'=>$result_pay[0]['payment_links_name'],'email'=>$result_pay[0]['payment_links_email'],'mobile'=>$result_pay[0]['payment_links_mobile'],'amount'=>$result_pay[0]['payment_links_amount'],'message'=>$result_pay[0]['remark'],'order_pay_status'=>$result_pay[0]['status'],'payment_id'=>$result_pay[0]['transaction_id'],'date'=>$result_pay[0]['created_at']];
					/*------------------Mail Data Array------------------*/

					/*------------------User Start for mail function------------------*/
					$template_name='direct_payment_order_user';
					$subject='Thank you for Payment-Format for Payment done';
					$mail_for='User';
					$to_mail=$result_pay[0]['payment_links_email'];
					$data=['template_name'=>$template_name,'send_data_arary'=>$send_data_arary,'subject'=>$subject,'mail_for'=>$mail_for,'to_mail'=>$to_mail];
					$this->app->utility->sendMial($data);
					/*------------------User End for mail function------------------*/


					/*------------------Admin Start for mail function------------------*/
					$template_name='direct_payment_order_admin';
					$subject='Online payment From '.$result_pay[0]['payment_links_name'].' on Website';
					$mail_for='Admin';
					$data=['template_name'=>$template_name,'send_data_arary'=>$send_data_arary,'subject'=>$subject,'mail_for'=>$mail_for];
					$this->app->utility->sendMial($data);
					/*------------------Admin End for mail function------------------*/

					//send push notification
					$data['employee_ids']=$result_pay[0]['payment_links_employee_id'];
					$title='Payment Recived of Rs. '.$result_pay[0]['payment_links_amount'];
					$message='Against Payment Link #'.$result_pay[0]['payment_links_id'];
					$data['notification']=array('title'=>$title,'image'=>'','message'=>$message,'type'=>'','body'=>$message,'click_action'=>'PaymentLinkDetailsActivity');
					$this->app->utility->send_push($data);

					//notification data insert
					$data_t=array();
					$data_t['noti_type']='Payment Link';
					$data_t['title']=$title;
					$data_t['description']=$message;
					$data_t['employee_ids']=$result_pay[0]['payment_links_employee_id'];
					$data_t['table_id']=$result_pay[0]['payment_links_id'];
					$data_t['created_by']=$result_pay[0]['payment_links_employee_id'];
					$data_t['created_at']=date("Y-m-d H:i:s");
					$obj_model_employee_task_master=$this->app->load_model("notifications");
					$obj_model_employee_task_master->map_fields($data_t);
					$obj_model_employee_task_master->execute("INSERT");

					//redirect to success page
					$this->app->redirect('qr-payment-success/'.$order_id.'');
				}
				else 
				{
					$this->app->redirect('qr-payment-failed/'.$order_id.'');
				}
			}
			else
			{
				//$this->app->redirect(SERVER_ROOT);
			}
		}
		else {
			//$this->app->redirect(SERVER_ROOT);
		}
	}
}
?>