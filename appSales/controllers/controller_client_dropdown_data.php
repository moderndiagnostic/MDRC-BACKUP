<?
class _client_dropdown_data extends controller {
	function init() {
	}

	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$obj_model_city = $this->app->load_model("city");
		$city = $obj_model_city->execute("SELECT",false,"","city.status='Active'","city.name ASC");
		foreach($city as $item)
		{
			$cityList[]=["id"=>$this->app->utility->encrypt($item['id']),"name"=>$item['name'],"state_id"=>$this->app->utility->encrypt($item['state_id'])];
		}

		$obj_model_state = $this->app->load_model("state");
		$state = $obj_model_state->execute("SELECT",false,"","state.status='Active'","state.name ASC");
		foreach($state as $item)
		{
			$stateList[]=["id"=>$this->app->utility->encrypt($item['id']),"name"=>$item['name']];
		}

		$businessTypeList[]=["id"=>"Doctor","name"=>"Doctor"];
		$businessTypeList[]=["id"=>"Laboratory","name"=>"Laboratory"];
		$businessTypeList[]=["id"=>"B2B","name"=>"B2B"];
		$businessTypeList[]=["id"=>"Collection","name"=>"Collection"];
		$businessTypeList[]=["id"=>"Centre","name"=>"Centre"];
		$businessTypeList[]=["id"=>"CenHospitaltre","name"=>"Hospital"];

		$legalTypeList[]=["id"=>"PVT LTD","name"=>"PVT LTD"];
		$legalTypeList[]=["id"=>"Proprietor","name"=>"Proprietor"];

		$registerTypeList[]=["id"=>"Special case","name"=>"Special Case"];
		$registerTypeList[]=["id"=>"Normal","name"=>"Normal"];

		$result=["cityList"=>$cityList,"stateList"=>$stateList,"businessTypeList"=>$businessTypeList,"legalTypeList"=>$legalTypeList,"registerTypeList"=>$registerTypeList];
		$message=array("message"=>"success","msgCode"=>"1","result"=>$result);
		
		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>