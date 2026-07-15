<?
class _webview_payu_payment_fail extends controller {
	function init(){
	}
	function onload()
	{
		$id=$this->app->getPostVar('udf1');
		$status=$this->app->getPostVar('status');

		$obj_model_employee = $this->app->load_model("employee_sample_pickup_payment");
		$paymentData=$obj_model_employee->execute("SELECT",false,"","id='".$id."'");

		if ($status=='failure') {
			$data_t=array();	
			$data_t['status']='Fail';
			$data_t['transaction_date']=date('Y-m-d H:i:s');
			$obj_model_employee=$this->app->load_model("employee_sample_pickup_payment");
			$obj_model_employee->map_fields($data_t);
			$obj_model_employee->execute("UPDATE",false,"","id='".$id."'");
		}
		$this->app->assign("paymentData", $paymentData);
	}
}
?>