<?
class _logistic_assign_client_pickup_sample_list extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$samplePickupID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('samplePickupID'));
		$samplePickupID=$this->app->utility->decrypt($samplePickupID);
		
		if($employeeID!='' && $samplePickupID!='')
		{
			$obj_model_client = $this->app->load_model("employee_sample_pickup_images");
			$obj_model_client->join_table("employee_sample_pickup", "left", array(), array("employee_sample_pickup_id"=>"id"));
			$sample = $obj_model_client->execute("SELECT",false,"","employee_sample_pickup_images.employee_id='".$employeeID."' and employee_sample_pickup_images.employee_sample_pickup_id='".$samplePickupID."'","employee_sample_pickup_images.id ASC");

			$pickupData[]=["title"=>'#'.$samplePickupID,"value"=>$sample[0]['employee_sample_pickup_status']];
			$pickupData[]=["title"=>'Date',"value"=>date('d-m-Y h:i A', strtotime($sample[0]['employee_sample_pickup_created_at']))];
			$pickupData[]=["title"=>'Total Sample',"value"=>count($sample)." Collected"];


			foreach($sample as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'samplePickup/'.$item['employee_sample_pickup_id'],'large');
				$pickupSampleList[]=array(
					"id"=>$id,
					"employee_sample_pickup_id"=>$item['master_no'],
					"d_id"=>$item['id'],
					"parent_id"=>$item['parent_id']>0?$item['parent_id']:'',
					"number"=>''.$item['id'],
					"detail"=>''.$item['id'],
					"image"=>$image,
					"barcode"=>$item['barcode'],
					"remark"=>$item['remark']??'',
					"date"=>date('d-m-Y h:i A', strtotime($item['updated_at'])),
				);
			}
			$result=["pickupSampleList"=>$pickupSampleList,"pickupData"=>$pickupData];
			$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		}
		else
		{
			$message=array("message"=>"Oops Something Gone Wrong. Try again...","msgCode"=>"0");
		}
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>