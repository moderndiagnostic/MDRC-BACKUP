<?
class _notifications extends controller {
	function init() {
	}

	function onload() 
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$employeePhone=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeePhone'));
		$deviceType=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("deviceType"));

		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		//$page=!empty($page)?(int)$page:0;
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
		$type=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("type"));

		$whereCond='(employee_ids="" or FIND_IN_SET("'.$employeeID.'", employee_ids))';
		if($employeeID!='' && $deviceType!='')
		{
			if($search!='') {
				$whereCond.=" and (title LIKE '%$search%' or description LIKE '%$search%')";
			}
			if($type!='') {
				$whereCond.=" and noti_type='".$type."'";
			}

			//count start
			$typeList[]=["key"=>"","value"=>"All"];
			$typeList[]=["key"=>"Task","value"=>"Task"];
			$typeList[]=["key"=>"Logistic Assign","value"=>"Logistic Assign"];
			$typeList[]=["key"=>"Other","value"=>"Other"];
			
			//count end

			$obj_model_task = $this->app->load_model("notifications");
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"notifications.id desc");
			$count=count($task);

			$limit=10;
			$total_pages=intval($count/$limit);

			if($count<=0 || $total_pages<$page) {
				$result=["typeList"=>$typeList];
				$message=array("message"=>"No Notification Found.","msgCode"=>"0","result"=>$result);
				$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
				echo $this->app->utility->indent($opt); exit;
			}

			$start=$page==0?0:($page)*$limit;

			$obj_model_task = $this->app->load_model("notifications");
			//$obj_model_task->join_table("employee", "left", array(), array("assign_by_employee_id"=>"id"));
			$task = $obj_model_task->execute("SELECT",false,"",$whereCond,"notifications.id desc limit ".$start.",".$limit."");

			foreach($task as $item)
			{
				$id=$this->app->utility->encrypt($item['id']);
				if($item['noti_type']=='Task') {
					$buttonName='View Task';
					$buttonId=$this->app->utility->encrypt($item['table_id']);
				} else if($item['noti_type']=='Logistic Assign') {
					$buttonName='View Request';
					$buttonId=$this->app->utility->encrypt($item['table_id']);
				} else if($item['noti_type']=='Client Detail') {
					$buttonName='View Detail';
					$buttonId=$this->app->utility->encrypt($item['table_id']);
				} else if($item['noti_type']=='Sample Dispatch Detail') {
					$buttonName='View Detail';
					$buttonId=$this->app->utility->encrypt($item['table_id']);
				} else if($item['noti_type']=='Leave Request') {
					$buttonName='View All Request';
					$buttonId=0;
				} else if($item['noti_type']=='Temporary Client Assign') {
					$buttonName='Pickup';
					$buttonId=0;
				}else if($item['noti_type']=='Payment Link') {
					$buttonName='View Payment';
					$buttonId=$this->app->utility->encrypt($item['table_id']);
				}else {
					$buttonName='';
				}
				
				$notificationList[]=array(
					"id"=>$id,
					"type"=>$item['noti_type'],
					"title"=>$item['title'],
					"description"=>$item['description'],
					"buttonName"=>$buttonName,
					"buttonId"=>$buttonId,
					"createdOn"=>date('d-m-Y', strtotime($item['created_at']))
				);
			}
			$result=["notificationList"=>$notificationList,"typeList"=>$typeList];
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