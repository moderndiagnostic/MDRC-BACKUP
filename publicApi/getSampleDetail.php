<?
	define("VIR_DIR","publicApi");
	include("../core/app.php");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	$app = & app::get_instance();
	$app->initialize();

	$pickupMasterNo=$app->getPostVar("pickupMasterNo");
	if(empty($pickupMasterNo))
	{	
		$message=array("message"=>"Data is missing.","msgCode"=>"0","result"=>[]);

		$response=$message;
		$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
		$final_response=$app->utility->indent($opt);
		echo $final_response; exit;
	}

	$list=[];
	
	$obj_model_client = $app->load_model("employee_sample_pickup_images");
	$itemList = $obj_model_client->execute("SELECT",false,"","parent_id='".$pickupMasterNo."'","id desc");

	if(count($itemList)==0)
	{	

		$obj_model_client = $app->load_model("employee_sample_pickup_images");
		$itemList = $obj_model_client->execute("SELECT",false,"","id='".$pickupMasterNo."'","id desc");

		if(count($itemList)==0)
		{	
			$message=array("message"=>"No data found","msgCode"=>"0","result"=>[]);
			$response = curl_exec($curl);

			$response=$message;
			$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
			$final_response=$app->utility->indent($opt);
			echo $final_response; exit;
		}

		foreach($itemList as $item)
		{
			$image=$app->utility->get_image_url($item["image"],'samplePickup/'.$item['employee_sample_pickup_id'],'large');
			$list[]=[
				"image"=>$image,
				"barcode"=>$item['barcode'],
				"remark"=>$item["remark"]??'',
				"date"=>date('d-m-Y h:i A',strtotime($item['updated_at']))
			];
		}

		$obj_model_client = $app->load_model("employee");
		$obj_model_client->join_table("employee_centre", "left", array(), array("id"=>"employee_id"));
		$obj_model_client->join_table(["employee_centre"=>"employee_centre","master_centre"=>"master_centre"], "left", array(), array("lms_centre_id"=>"lms_center_id"));
		$employee = $obj_model_client->execute("SELECT",false,"","employee.id='".$itemList[0]['employee_id']."'");

		$obj_model_client = $app->load_model("client");
		$client = $obj_model_client->execute("SELECT",false,"","id='".$itemList[0]['client_id']."'");



		$employeeData=[
			"code"=>$employee[0]['lms_employee_code']??'',
			"name"=>$employee[0]['name']??'',
			"mobile"=>$employee[0]['mobile']??'',
			"center"=>$employee[0]['master_centre_name']??'',
			"client"=>$client[0]['company_name']??'',
		];

		
		
		$result=["list"=>$list,"employee"=>$employeeData];
		$message=array("message"=>"Success","msgCode"=>"1","result"=>$result);
	}
	else
	{
		foreach($itemList as $item)
		{
			$image=$app->utility->get_image_url($item["image"],'samplePickup/'.$item['employee_sample_pickup_id'],'large');
			$list[]=[
				"image"=>$image,
				"barcode"=>$item['barcode'],
				"remark"=>$item["remark"]??'',
				"date"=>date('d-m-Y h:i A',strtotime($item['updated_at']))
			];
		}

		$obj_model_client = $app->load_model("employee");
		$obj_model_client->join_table("employee_centre", "left", array(), array("id"=>"employee_id"));
		$obj_model_client->join_table(["employee_centre"=>"employee_centre","master_centre"=>"master_centre"], "left", array(), array("lms_centre_id"=>"lms_center_id"));
		$employee = $obj_model_client->execute("SELECT",false,"","employee.id='".$itemList[0]['employee_id']."'");

		$obj_model_client = $app->load_model("client");
		$client = $obj_model_client->execute("SELECT",false,"","id='".$itemList[0]['client_id']."'");
		
		$employeeData=[
			"code"=>$employee[0]['lms_employee_code']??'',
			"name"=>$employee[0]['name']??'',
			"mobile"=>$employee[0]['mobile']??'',
			"center"=>$employee[0]['master_centre_name']??'',
			"client"=>$client[0]['company_name']??'',
		];
		
		$result=["list"=>$list,"employee"=>$employeeData];
		$message=array("message"=>"Success","msgCode"=>"1","result"=>$result);
	}

	

	

	$response=$message;
	$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
	$final_response=$app->utility->indent($opt);
	echo $final_response; exit;
	$app->unload();
?>