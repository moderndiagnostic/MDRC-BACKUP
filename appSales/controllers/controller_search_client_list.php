<?
class _search_client_list extends controller {
	function init() {
	}

	function onload()
	{
		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		
		$whereCond='';
		if($page!='')
		{
			$whereCond .= " AND (client.company_name LIKE '%{$search}%' OR client.mobile LIKE '%{$search}%' OR client_detail.address LIKE '%{$search}%')";

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc");
			$count=count($client);

			$limit=30;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$message=array("message"=>"No Client Found.","msgCode"=>"0");
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_client = $this->app->load_model("client");
			$obj_model_client->join_table("city", "left", array("name"), array("city_id"=>"id"));
			$obj_model_client->join_table("client_detail", "left", array("area"), array("id"=>"client_id"));
			$obj_model_client->join_table("client_address", "left", array("google_address"), array("id"=>"client_id"));
			$client = $obj_model_client->execute("SELECT",false,"","client.status='Active' ".$whereCond."","client.id desc limit ".$start.",".$limit."");

			$clientList=[];
			foreach($client as $item)
			{
				$address=$item['client_status']=='Client'?$item['client_detail_area'].' '.$item['city_name']:$item['client_address_google_city'];
				$id=$this->app->utility->encrypt($item['id']);
				$image=$this->app->utility->get_image_url($item["image"],'client','large');
				$clientList[]=array(
					"id"=>$id,
					"companyName"=>$item['company_name'],
					"mobile"=>$item['mobile'],
					"image"=>$image,
					"address"=>$address??'',
					"tagName"=>$item['client_status'],
					"tagColor"=>'#5ccdde'
				);
			}
			$result=["clientList"=>$clientList];
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