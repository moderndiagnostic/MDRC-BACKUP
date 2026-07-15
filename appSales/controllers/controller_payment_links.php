<?
include '../modules/phpqrcode/qrlib.php';

class _payment_links extends controller 
{
	function init() {
	}

	function onload() {
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$name=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("name"));
		$email=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("email"));
		$mobile=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("mobile"));
		$amount=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("amount"));
		$remark=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("remark"));

		$action=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("action"));

		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		$paymentStatus=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentStatus"));
		$paymentID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("paymentID"));
		$paymentID=$this->app->utility->decrypt($paymentID);
		
		if($employeeID=='') {
			$message=array("message"=>"Data Missing","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		if($name!='' && $email!='' && $amount!='' && $mobile!='' && $action=='create') 
		{
			$data_t=array();
			$data_t['employee_id']=$employeeID;
			$data_t['name']=$name;
			$data_t['email']=$email;
			$data_t['mobile']=$mobile;
			$data_t['amount']=$amount;
			$data_t['remark']=$remark;
			$data_t['image']=$remark;
			$data_t['created_at']=date("Y-m-d H:i:s");
			$obj_model_payment_links=$this->app->load_model("payment_links");
			$obj_model_payment_links->map_fields($data_t);
			$payment_id=$obj_model_payment_links->execute("INSERT",false,"","");	

			//generate QR
			$PNG_TEMP_DIR = ABS_PATH . '/uploads/qrcode/payLink/';
			$filename = $PNG_TEMP_DIR . $payment_id. '.png';
			$code = 'https://www.mdrcindia.com/SalesApp/qr-payment/'.$payment_id;
			QRcode::png($code, $filename, 'L', '3', '1');

			$result=["payment_id"=>$this->app->utility->encrypt($payment_id)];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		} 
		else if($action=='list')
		{
			$whereCond=" employee_id='".$employeeID."'";
			if($search!='') {
				$whereCond.=" and ((name LIKE '%$search%') or (amount LIKE '%$search%') or (mobile LIKE '%$search%'))";
			}
			if($paymentStatus!='') {
				$whereCond.=" and status='".$paymentStatus."'";
			}

			//count start
			$query="SELECT COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS Pending, COUNT(CASE WHEN status = 'Success' THEN 1 END) AS Success FROM payment_links where employee_id='".$employeeID."'";
			
			$obj_model_task = $this->app->load_model("payment_links");
			$paymentStatusResult = $obj_model_task->execute("SELECT",false,$query);
			
			$paymentStatusList[]=["key"=>"Pending","value"=>"Pending (".$paymentStatusResult[0]['Pending'].")"];
			$paymentStatusList[]=["key"=>"Success","value"=>"Success (".$paymentStatusResult[0]['Success'].")"];
			//count end

			$obj_model_task = $this->app->load_model("payment_links");
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"id desc");
			$count=count($task);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["taskStatus"=>$paymentStatusList];
				$message=array("message"=>"No Record Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("payment_links");
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$Color=$this->getStatus($item['status']);
				$id=$this->app->utility->encrypt($item['id']);
				$allTaskList[]=array("id"=>$id,"number"=>'#'.$item['id'],"status"=>$item['status'],
				"name"=>$item['name'],
				"email"=>$item['email'],
				"mobile"=>$item['mobile'],
				"amount"=>$item['amount'],
				"createdOn"=>date('d-m-Y', strtotime($item['created_at'])),
				"textStatusColor"=>$Color['text'],
				"textStatusBgColor"=>$Color['background']
				);
			}
			$result=["allPaymentList"=>$allTaskList,"paymentStatus"=>$paymentStatusList];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else if($employeeID!='' && $deviceType!='' && $paymentID!='' && $action=='details')
		{
			$obj_model_payment_links = $this->app->load_model("payment_links");
			$paymentDetail = $obj_model_payment_links->execute("SELECT",false,"","id='".$paymentID."'","id desc");

			if(count($paymentDetail)<=0) {
				$message=array("message"=>"No Record Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$obj_model_payment_links = $this->app->load_model("payment_link_transaction");
			$Detail=$obj_model_payment_links->execute("SELECT",false,"","payment_link_id='".$paymentID."'","id desc");
			if(count($Detail)>0)
			{
				foreach($Detail as $item)
				{
					$Color=$this->getStatus($item['status']);
					$detail[]=array(
						"transaction_id"=>'#'.$item['id'],
						"transaction_number"=>$item['transaction_id'],
						"transaction_date"=>date('d-m-Y H:i:s',strtotime($item['created_at'])),
						"transaction_status"=>$item['status'],
						"transaction_remark"=>$item['remark'],
						"textStatusColor"=>$Color['text'],
						"textStatusBgColor"=>$Color['background']);
				}
			}
			else
			{
				
				$detail=[];
			}
			$image=$this->app->utility->get_image_url($paymentDetail[0]['id'].'.png','qrcode/payLink','large');

			$result=[
				"id"=>$this->app->utility->encrypt($paymentDetail[0]['id']),
				"number"=>'#'.$paymentDetail[0]['id'],
				"image"=>$image,
				"name"=>$paymentDetail[0]['name'],
				"email"=>$paymentDetail[0]['email'],
				"mobile"=>$paymentDetail[0]['mobile'],
				"amount"=>$paymentDetail[0]['amount'],
				"status"=>$paymentDetail[0]['status'],
				"remark"=>$paymentDetail[0]['remark'],
				"detail"=>$detail
			];
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
		if($status=='Pending')
		{
			$textStatusColor='#1c273c';
			$textStatusBgColor='#ffc107';
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