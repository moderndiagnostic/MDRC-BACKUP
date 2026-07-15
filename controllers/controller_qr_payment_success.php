<?

class _qr_payment_success extends controller{

	function init(){

	}

	function onload()
	{
		$id=$this->app->getGetVar("id");

		$obj_model_payment_data=$this->app->load_model("payment_link_transaction");
		$obj_model_payment_data->join_table("payment_links", "left", array(), array("payment_link_id"=>"id"));
		$result_pay=$obj_model_payment_data->execute("SELECT",false,"","payment_link_transaction.id='".$id."'");

		$this->app->assign("payment",$result_pay[0]);
	}
}
?>