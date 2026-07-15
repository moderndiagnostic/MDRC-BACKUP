<?
	define("VIR_DIR","scripts/autofiles/");
	include("../../core/app.php");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	$app = & app::get_instance();
	$app->initialize();

	
	
	$obj_model_employee = $app->load_model('hrms_employee');
	$rs_employee=$obj_model_employee->execute("SELECT",false,"","hrms_status=1");
	if(count($rs_employee)>0)
	{
		foreach($rs_employee as $key=>$employee)
		{
			$obj_model_employee = $app->load_model('hrms_employee_salary');
			$hrms_employee_salary=$obj_model_employee->execute("SELECT",false,"","hrms_employee_id='".$employee['id']."' and salary_month='".date('m', strtotime('-1 month'))."' and salary_year='".date('y')."'");
			if(count($hrms_employee_salary)==0)
			{
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.pockethrms.com/api/EmployeeMaster/GetLastMonthSalary?employeeCode='.$employee['mdrc_code'],
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Authorization: VRrDX2daqa8Q18NZqlY9Zc4cQ1WOiXcrhXu0uqWoxFJnDALW wgJEBCSQe1x8Xp2VZGsM19pdKJEMONBkh8er//RTfoRle5KO FXh bInQ6w2dpsPiGJohyYbPO1O89QOB/umAfPiePvmkWqzlNxQJeU3fkxEDdoswG30Bw2ngQxCY988HDHWRdKUz4 Xp8Rvt 99t iI1 fBpPAQtJ04SJtyoVEfcjRdrXCs72ix9g=',
						'Content-Type: application/json',
					),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				$response=json_decode($response,true);
				
				if(count($response)>0)
				{
					$data_p=array();
					$data_p['hrms_employee_id']=$employee['id'];
					$data_p['salary_year']=date('y');
					$data_p['salary_month']=date('m', strtotime('-1 month'));
					$data_p['salary_amount']=$response[0]['Netpay'];
					$data_p['salary_gross_amount']=$response[0]['Gross'];
					$data_p['salary_td_amount']=$response[0]['TD'];
					$data_p['created_at']=date('Y-m-d H:i:s');									
					$data_p['updated_at']=date('Y-m-d H:i:s');
	
					$obj_model_productn = $app->load_model('hrms_employee_salary');
					$obj_model_productn->map_fields($data_p);
					$obj_model_productn->execute("INSERT",false,"","");
		
				}
			}
		}
		$message=array("message"=>"Record added successfully.","msgcode"=>"0");
		$data=['template_name'=>'hrms_sync_admin','send_data_arary'=>['message'=>'Employee Salary Sync Successfully.'],'subject'=>'Employee Salary Sync Successfully.','mail_for'=>'Admin'];
		$app->utility->sendMial($data);
	}
	else
	{		
		$message=array("message"=>"Record Not Found.","msgcode"=>"1");
	}
	$response=$message;
	$opt=json_encode($response, JSON_UNESCAPED_UNICODE);
	$final_response=$app->utility->indent($opt);
	echo $final_response; exit;
	$app->unload();
?>