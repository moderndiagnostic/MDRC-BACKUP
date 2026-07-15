<?
class _employee_daily_journey_detail extends controller
{
	function init()
	{
		###
	}
	function onload()
	{
		$journeyID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('journeyID'));
		$employeeID=mysqli_real_escape_string($this->app->set_db_conn(),$this->app->getPostVar('employeeID'));
		$employeeID=$this->app->utility->decrypt($employeeID);
	
		if($journeyID=='')
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

		$employee_manager_view='No';
		## FINANCE MANAGER
		$obj_manager = $this->app->load_model("employee_journey_finance_manager");
		$data_manager = $obj_manager->execute("SELECT",false,"","employee_id='".$employee[0]['id']."'");

		## CHECK FINANCE MANAGER
		if(count($data_manager)>0)
		{
			$employee_manager_view='Yes';
		}
		elseif($employee[0]['master_designation_id']==11)
		{
			$employee_manager_view='Yes';
		}

		$obj_model_employee_daily_journey = $this->app->load_model("employee_daily_journey");
		$obj_model_employee_daily_journey->join_table("employee_daily_journey_detail", "left", array(), array("id"=>"employee_daily_journey_id"));
		$obj_model_employee_daily_journey->join_table("employee", "left", array("name","lms_employee_code"), array("employee_id"=>"id"));
		$rs_employee_daily_journey = $obj_model_employee_daily_journey->execute("SELECT",false,"","employee_daily_journey.id='".$journeyID."'","employee_daily_journey.id desc");

		foreach($rs_employee_daily_journey as $item)
		{
			$employee_id=$this->app->utility->encrypt($item['employee_id']);
			if($item['employee_image']!=''){
				$empImage = SERVER_ROOT.'/uploads/employee/'.$item['employee_image'];
			} else {
				$empImage = '';
			}
			if($item['start_image']!=''){
				$startImage = SERVER_ROOT.'/uploads/daily_journey/'.$item['start_image'];
			} else {
				$startImage = '';
			}
			if($item['end_image']!=''){
				$endImage = SERVER_ROOT.'/uploads/daily_journey/'.$item['end_image'];
			} else {
				$endImage = '';
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
			
			$journeyDetail[]=array(
				"id"=>$item['id'],
				"employee_id"=>$employee_id,
				"employee_image"=>$empImage,
				"employee_name"=>$item['employee_name'],
				"employee_lms_employee_code"=>$item['employee_lms_employee_code'],
				"employee_manager_view"=>$employee_manager_view,
				"logistics_manager_title"=>$l_m_title,
				"logistics_manager_type"=>$l_m_type,
				"logistics_manager_button"=>$l_m_button,
				"finance_manager_title"=>$f_m_title,
				"finance_manager_type"=>$f_m_type,
				"finance_manager_button"=>$f_m_button,
				"journey_date"=>date('d-m-Y',strtotime($item['journey_date'])),
				"journey_start_time"=>date('h:i A',strtotime($item['start_datetime'])),
				"start_image"=>$startImage,
				"start_km"=>$item['start_km'],
				"start_latitude"=>$item['start_latitude'],
				"start_longitude"=>$item['start_longitude'],
				"journey_end_time"=>!empty($item['end_datetime']) ? date('h:i A',strtotime($item['end_datetime'])) : '',
				"end_image"=>$endImage,
				"end_km"=>!empty($item['end_km'])?$item['end_km']:"",
				"end_latitude"=>!empty($item['end_latitude'])?$item['end_latitude']:"",
				"end_longitude"=>!empty($item['end_longitude'])?$item['end_longitude']:"",
				"total_km"=>!empty($item['total_km'])?$item['total_km']:"",
				"status"=>$item['status'],
				"manager_remark"=>!empty($item['employee_daily_journey_detail_manager_remark'])?$item['employee_daily_journey_detail_manager_remark']:"",
				"finance_remark"=>!empty($item['employee_daily_journey_detail_finance_remark'])?$item['employee_daily_journey_detail_finance_remark']:"",
				"finance_datetime"=>!empty($item['employee_daily_journey_detail_finance_datetime'])?date('d-m-Y h:i A',strtotime($item['employee_daily_journey_detail_finance_datetime'])):"",
				"manager_datetime"=>!empty($item['employee_daily_journey_detail_manager_datetime'])?date('d-m-Y h:i A',strtotime($item['employee_daily_journey_detail_manager_datetime'])):"",
			);

			### GET SAMPLE LIST
			$obj_sample_pickup = $this->app->load_model("employee_sample_pickup");
			$obj_sample_pickup->join_table("client", "left", array("company_name","email","mobile"), array("client_id"=>"id"));
			$rs_sample_pickup = $obj_sample_pickup->execute("SELECT",false,"","employee_sample_pickup.employee_id='".$item['employee_id']."' and employee_sample_pickup.pickup_date='".date('d-m-Y',strtotime($item['journey_date']))."'","employee_sample_pickup.id asc");
			
			$samplePickupList=[];
			$totalKm = 0;
			if(count($rs_sample_pickup)>0) {
				foreach($rs_sample_pickup as $sample){
					$distance = floatval(str_replace(" km", "", $sample['distance_km']));
        			$totalKm += $distance;

					$samplePickupList[] = [
						'distance' => $sample['distance_km'],
						'client' => $sample['client_company_name'],
						'collect_sample' => $sample['collect_sample'],
						'collect_payment' => $sample['collect_payment'],
					];
				}
			}
		}
		$result=["journeyDetail"=>$journeyDetail,"samplePickupList"=>$samplePickupList,"totalKm"=>number_format($totalKm,2)];
		$message=array("message"=>"success","msgCode"=>"1","result"=>$result);

		$opt=json_encode($message, JSON_UNESCAPED_UNICODE);
		echo $this->app->utility->indent($opt);
		exit;
	}
}
?>