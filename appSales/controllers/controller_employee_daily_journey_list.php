<?
class _employee_daily_journey_list extends controller
{
	function init()
	{
		###
	}
	function onload()
	{
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
		$page=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("page"));
		$search=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar("search"));
	
		if($employeeID=='')
		{
			$message=array("message"=>"Data is missing.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$obj_model_employee = $this->app->load_model("employee");
		$employee = $obj_model_employee->execute("SELECT",false,"","id='".$employeeID."' and employee.status!='Trash'","employee.id desc");
		if(count($employee)<=0) { 
			$message=array("message"=>"No Employee Found.","msgCode"=>"0");
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$whereCond='';
		$employee_manager_view='No';
		// 12 no logistic and 11 no logistic manager
		
		## FINANCE MANAGER
		$obj_manager = $this->app->load_model("employee_journey_finance_submanager");
		$obj_manager->join_table("employee_journey_finance_manager", "left", array(), array("employee_journey_finance_manager_id"=>"id"));
		$data_manager = $obj_manager->execute("SELECT",false,"","employee_journey_finance_manager.employee_id='".$employee[0]['id']."'");

		## CHECK FINANCE MANAGER
		if(count($data_manager)>0)
		{
			# GET MANAGER
			$obj_employee = $this->app->load_model("employee");
			$whereConds='id IN ('.implode(',',array_column($data_manager,'employee_id')).')';
			$employeeNew = $obj_employee->execute("SELECT",false,"","$whereConds and employee.status!='Trash'");
			$getLmsEmp=array_column($employeeNew,'lms_employee_id');

			$allEmp=[];
			foreach($getLmsEmp as $emp)
			{
				$obj_model_employee = $this->app->load_model("employee");
				$employee55 = $obj_model_employee->execute("SELECT",false,"","reporting_employee_lms_id='".$emp."'");
				if (!empty($employee55)) {
					$ids = array_column($employee55, 'id'); // extract 'id's
					$allEmp = array_merge($allEmp, $ids);     // merge into flat array
				}
			}
			$empstr = implode(',',$allEmp);
			$whereCond='employee_id IN ('.$empstr.')';
			$employee_manager_view='Yes';
		}
		elseif($employee[0]['master_designation_id']==11)
		{
			// 11 find employee reporting employee lms id
			$obj_model_employee = $this->app->load_model("employee");
			$employeeAll = $obj_model_employee->execute("SELECT",false,"","reporting_employee_lms_id='".$employee[0]['lms_employee_id']."' and employee.status!='Trash'","employee.id desc");
			if(count($employeeAll)>0)
			{
				$whereCond='employee_id IN ('.implode(',',array_column($employeeAll,'id')).')';
			}
			else
			{
				$whereCond='employee_id=0';	
			}
			$employee_manager_view='Yes';
		}
		elseif($employee[0]['master_designation_id']==12)
		{
			$whereCond='employee_id="'.$employeeID.'"';
		}
		else
		{
			$whereCond='employee_id=0';
		}

		// $whereCond.=!empty($search)?' and journey_date="'.date('Y-m-d', strtotime($search)).'"':'';
		$whereCond .= !empty($search) ? " AND (employee.name LIKE '%" . $search . "%' OR employee.lms_employee_code LIKE '%" . $search . "%')" : "";
		$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
		$obj_model_employee_daily_journey->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
		$employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"",$whereCond,"id desc");
		$count=count($employee_daily_journey);
		
		$limit=10;
		$total_pages=intval($count/$limit);

		if($count<=0 || $total_pages<$page) {
			$journeyList=array();
			$result=["journeyList"=>$journeyList];
			$message=array("message"=>"No Data Found.","msgCode"=>"0","result"=>$result);
			$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
			echo $this->app->utility->indent($opt); exit;
		}

		$start=$page==0?0:($page)*$limit;

		$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
		$obj_model_employee_daily_journey->join_table("employee_daily_journey_detail", "left", array(), array("id"=>"employee_daily_journey_id"));
		$obj_model_employee_daily_journey->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
		$rs_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"",$whereCond,"employee_daily_journey.id desc limit ".$start.",".$limit."");

		foreach($rs_employee_daily_journey as $item)
		{
			$employee_id=$this->app->utility->encrypt($item['employee_id']);
			if($item['employee_image']!=''){
				$empImage = SERVER_ROOT.'/uploads/employee/'.$item['employee_image'];
			} else {
				$empImage = '';
			}

			$l_m_title='Waiting For Approval';
			$l_m_type='Waiting';
			$l_m_button='No';
			$f_m_button='No';
			if($item['status']!='Running' && $employee[0]['master_designation_id']==12)
			{
				$l_m_button='No';
				$f_m_button='No';
			}
			else if($item['status']!='Running' && $employee[0]['master_designation_id']==11)
			{
				$l_m_button='Yes';
				$f_m_button='No';
			}
			else
			{
				$l_m_button='No';
				$f_m_button=$item['status']=='Approve By Manager'?'Yes':'No';
			}
			if($item['employee_daily_journey_detail_manager_employee_id']>0)
			{
				if($item['status']=='Approve By Manager' || $item['status']=='Approve By Finance' || $item['status']=='Reject By Finance')
				{
					$l_m_title='Approved By Manager';
					$l_m_type='Approve';
					$l_m_button='No';
				}
				else
				{
					$l_m_title='Rejected By Manager';
					$l_m_type='Reject';
					$l_m_button='No';
				}
			}

			if($item['status']=='Reject By Manager')
			{
				$f_m_title='';
				$f_m_type='';
			}
			else
			{
				$f_m_title='Waiting For Approval';
				$f_m_type='Waiting';
			}
			if($item['employee_daily_journey_detail_manager_employee_id']>0 && $item['employee_daily_journey_detail_finance_employee_id']>0)
			{
				if($item['status']=='Approve By Finance')
				{
					$f_m_title='Approved By Finance Manager';
					$f_m_type='Approve';
					$f_m_button='No';
				}
				else
				{
					$f_m_title='Rejected By Finance Manager';
					$f_m_type='Reject';
					$f_m_button='No';
				}
			}

			$journeyList[]=array(
				"id"=>$item['id'],
				"employee_image"=>$empImage,
				"employee_name"=>$item['employee_name'],
				"employee_code"=>$item['employee_lms_employee_code'],
				"employee_manager_view"=>$employee_manager_view,
				"logistics_manager_title"=>$l_m_title,
				"logistics_manager_type"=>$l_m_type,
				"logistics_manager_button"=>$l_m_button,
				"finance_manager_title"=>$f_m_title,
				"finance_manager_type"=>$f_m_type,
				"finance_manager_button"=>$f_m_button,
				"employee_id"=>$employee_id,
				"journey_date"=>!empty($item['journey_date'])?date('d-m-Y',strtotime($item['journey_date'])):'',
				"start_time"=>!empty($item['start_datetime'])?date('h:i A',strtotime($item['start_datetime'])):'',
				"end_time"=>!empty($item['end_datetime'])?date('h:i A',strtotime($item['end_datetime'])):'',
				"start_km"=>$item['start_km'],
				"end_km"=>$item['end_km'],
				"start_latitude"=>$item['start_latitude'],
				"start_longitude"=>$item['start_longitude'],
				"end_latitude"=>$item['end_latitude'],
				"end_longitude"=>$item['end_longitude'],
				"total_km"=>$item['total_km'],
				"start_image"=>!empty($item['start_image'])?$item['image_path'].$item['start_image']:'',
				"end_image"=>!empty($item['end_image'])?$item['image_path'].$item['end_image']:'',
				"status"=>$item['status']
			);
		}
		$result=["journeyList"=>$journeyList];
		$message=array("message"=>"success","msgCode"=>"1","result"=>$result);

		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>