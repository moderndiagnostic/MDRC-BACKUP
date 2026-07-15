<?
include '../modules/phpqrcode/qrlib.php';

class _sample_collect_payment_list extends controller 
{
	function init() {
	}

	function onload() {
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);

		$clientID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('clientID'));
		$clientID=$this->app->utility->decrypt($clientID);

		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));
		$amount=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("amount"));
		$remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("remark"));

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));
		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		$paymentStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentStatus"));
		$paymentID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentID"));
		$paymentMode=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentMode"));
		//$paymentID=$this->app->utility->decrypt($paymentID);
		
		if($employeeID=='') {
			
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($employeeID!='' && $clientID!='' && $amount!='' && $action=='create') 
		{
			$txnid = 'TXN-' . uniqid();
			$data_t=array();
			$data_t['employee_id']=$employeeID;
			$data_t['client_id']=$clientID;
			$data_t['transaction_id']=$txnid;
			$data_t['amount']=$amount;
			$data_t['transaction_date']=date("Y-m-d H:i:s");
			$data_t['created_at']=date("Y-m-d H:i:s");
			$data_t['payment_status']='Fail';
			$data_t['ip']=$_SERVER['REMOTE_ADDR'];
			$data_t['browser']=$_SERVER['HTTP_USER_AGENT'];
			$obj_model_client=$this->app->load_model("employee_sample_pickup_payment");
			$obj_model_client->map_fields($data_t);
			$pickup_payment=$obj_model_client->execute("INSERT",false,"","");

			if(!empty($paymentMode) && $paymentMode=='RAZORPAY')
			{
				$amount = number_format(1, '2', '.', '');
				$orderData = [
					'receipt'         => (string)$pickup_payment,
					'amount'          => $amount * 100,
					'currency'        => 'INR',
					'payment_capture' => 1 // auto capture
				];
				$url = "https://api.razorpay.com/v1/orders";
				$razor_order_id = $this->app->utility->razorpay_create_order($orderData, $url);

				$result=[
					"payment_id"=>$pickup_payment,
					"razor_order_id"=>$razor_order_id,
					"amount"=>$amount,
				];

				$message=array("message"=>"success","msgCode"=>"2","result"=>$result);
			}
			else
			{
				$paymentPageUrl=SERVER_ROOT.'/appSales/index.php?view=webview_payu_payment_collection&id='.$pickup_payment;
				$result=["payment_id"=>$this->app->utility->encrypt($pickup_payment),'paymentUrl'=>$paymentPageUrl];
				$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
			}
			
		} 
		else if($action=='list')
		{
			$whereCond = "employee_id = '".$employeeID."'";

			if ($search != '') {
				$whereCond .= " AND (client.company_name LIKE '%$search%' OR client.mobile LIKE '%$search%' OR employee_sample_pickup_payment.amount LIKE '%$search%')";
			}

			if ($paymentStatus != '' && $paymentStatus != 'All') {
				$whereCond .= " AND payment_status = '".$paymentStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN payment_status != '' THEN 1 END) AS all_count, COUNT(CASE WHEN payment_status = 'Fail' THEN 1 END) AS Fail, COUNT(CASE WHEN payment_status = 'Success' THEN 1 END) AS Success FROM employee_sample_pickup_payment where employee_id='".$employeeID."'";
			
			$obj_model_task=$this->app->load_model("employee_sample_pickup_payment");
			$paymentStatusResult=$obj_model_task->execute("SELECT",false,$query);
			
			$paymentStatusList[]=["key"=>"","value"=>"All (".$paymentStatusResult[0]['all_count'].")"];
			$paymentStatusList[]=["key"=>"Success","value"=>"Success (".$paymentStatusResult[0]['Success'].")"];
			$paymentStatusList[]=["key"=>"Fail","value"=>"Fail (".$paymentStatusResult[0]['Fail'].")"];
			
			//count end

			$obj_model_task = $this->app->load_model("employee_sample_pickup_payment");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_task->join_table("client", "left", array(), array("client_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"id desc");
			$count=count($task);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["paymentStatus"=>$paymentStatusList];
				$message=array("message"=>"No Record Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("employee_sample_pickup_payment");
			$obj_model_task->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_task->join_table("client", "left", array(), array("client_id"=>"id"));
			$obj_model_task->join_table("client_detail", "left", array("area"), array("client_id"=>"client_id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$Color=$this->getStatus($item['payment_status']);
				$client_id=$this->app->utility->encrypt($item['client_id']);
				$image=$this->app->utility->get_image_url($item["client_image"],'client','large');
				$allPaymentList[]=array(
					"id"=>$item['id'],
					"client_id"=>$client_id,
					"client_name"=>$item['client_company_name'],
					"client_address"=>$item['client_detail_area'].' '.$item['client_city_name'],
					"client_mobile"=>$item['client_mobile'],
					"client_image"=>$image,
					"lis_transaction_id"=>$item['lis_transaction_id'],
					"transaction_id"=>$item['transaction_id'],
					"transaction_date"=>!empty($item['transaction_date'])?date('d-m-Y', strtotime($item['transaction_date'])):'',
					"amount"=>$item['amount'],
					"status"=>$item['payment_status'],
					"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
					"textStatusColor"=>$Color['text'],
					"textStatusBgColor"=>$Color['background'],
					'paymentPageUrl'=>$item['payment_status']=='Fail'?SERVER_ROOT.'/appSales/index.php?view=webview_payu_payment_collection&id='.$item['id']:''
				);
			}
			$result=["allPaymentList"=>$allPaymentList,"paymentStatus"=>$paymentStatusList];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($employeeID!='' && $deviceType!='' && $paymentID!='' && $action=='detail')
		{
			$obj_model_employee_sample_pickup_payment = $this->app->load_model("employee_sample_pickup_payment");
			$obj_model_employee_sample_pickup_payment->join_table("employee", "left", array(), array("employee_id"=>"id"));
			$obj_model_employee_sample_pickup_payment->join_table("client", "left", array(), array("client_id"=>"id"));
			$paymentDetail = $obj_model_employee_sample_pickup_payment->execute("SELECT",false,"","employee_sample_pickup_payment.id='".$paymentID."'");

			if(count($paymentDetail)<=0) {
				$message=array("message"=>"No Record Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$item=$paymentDetail[0];
			$image=$this->app->utility->get_image_url($item["client_image"],'client','large');
			$client_id=$this->app->utility->encrypt($item['client_id']);
			$Color=$this->getStatus($item['payment_status']);
			$result=array(
				"id"=>$item['id'],
				"client_id"=>$client_id,
				"client_name"=>$item['client_company_name'],
				"client_address"=>$item['client_detail_area'].' '.$item['client_city_name'],
				"client_mobile"=>$item['client_mobile'],
				"client_image"=>$image,
				"lis_transaction_id"=>$item['lis_transaction_id'],
				"transaction_id"=>$item['transaction_id'],
				"transaction_date"=>!empty($item['transaction_date'])?date('d-m-Y', strtotime($item['transaction_date'])):'',
				"amount"=>$item['amount'],
				"status"=>$item['payment_status'],
				"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
				"textStatusColor"=>$Color['text'],
				"textStatusBgColor"=>$Color['background'],
				'paymentPageUrl'=>$item['payment_status']=='Fail'?SERVER_ROOT.'/appSales/index.php?view=webview_payu_payment_collection&id='.$item['id']:''
			);
			
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else
		{
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}

	function getStatus($status)
	{
		if($status=='Fail')
		{
			$textStatusColor='#ffffff';
			$textStatusBgColor='#dc3545';
		}
		elseif($status=='Success')
		{
			$textStatusColor='#ffffff';
			$textStatusBgColor='#10b759';
		}
		else
		{
			$textStatusColor='#ffffff';
			$textStatusBgColor='#dc3545';
		}
		$color=["text"=>$textStatusColor,"background"=>$textStatusBgColor];
		return $color;
	}
}
?>