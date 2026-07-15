<?
	define("VIR_DIR","scripts/autofiles/");
	include("../../core/app.php");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	$app = & app::get_instance();
	$app->initialize();

	
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.pockethrms.com/api/EmployeeMaster/GetEmployeeMaster',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization: VRrDX2daqa8Q18NZqlY9Zc4cQ1WOiXcrhXu0uqWoxFJnDALW wgJEBCSQe1x8Xp2VZGsM19pdKJEMONBkh8er//RTfoRle5KO FXh bInQ6w2dpsPiGJohyYbPO1O89QOB/umAfPiePvmkWqzlNxQJeU3fkxEDdoswG30Bw2ngQxCY988HDHWRdKUz4 Xp8Rvt 99t iI1 fBpPAQtJ04SJtyoVEfcjRdrXCs72ix9g=',
			'EmployeeFields: fname,Code,Designation,CostCentre,Location,Department',
			'Content-Type: application/json',
			'Take: 5000',
			'Offset: 1'
		),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	$response=json_decode($response,true);
	
	if(count($response)>0)
	{
		foreach($response as $hrmsEmployee)
		{
			if($hrmsEmployee['EmpStatus']==1)
			{
				$obj_model_employee = $app->load_model('hrms_employee');
				$rs_employee=$obj_model_employee->execute("SELECT",false,"","hrms_id='".$hrmsEmployee['Id']."'");
				
				if(count($rs_employee)==0)
				{
					$data_p=array();
					$data_p['hrms_id']=$hrmsEmployee['Id'];
					$data_p['name']=$hrmsEmployee['FName'];
					$data_p['hrms_status']=$hrmsEmployee['EmpStatus'];
					$data_p['mdrc_code']=$hrmsEmployee['Code'];
					$data_p['location']=$hrmsEmployee['LocationString'];
					$data_p['department']=$hrmsEmployee['DepartmentString'];
					$data_p['cost_center']=$hrmsEmployee['CostCentreString'];
					$data_p['designation']=!empty($hrmsEmployee['DesignationString'])?$hrmsEmployee['DesignationString']:'';
					$data_p['created_at']=date('Y-m-d H:i:s');									
					$data_p['updated_at']=date('Y-m-d H:i:s');
					
					$obj_model_productn = $app->load_model('hrms_employee');
					$obj_model_productn->map_fields($data_p);
					$obj_model_productn->execute("INSERT",false,"","");
				}
				else
				{
					$data_p=array();
					
					$data_p['name']=$hrmsEmployee['FName'];
					$data_p['hrms_status']=$hrmsEmployee['EmpStatus'];
					$data_p['mdrc_code']=$hrmsEmployee['Code'];
					$data_p['cost_center']=$hrmsEmployee['CostCentreString'];
					$data_p['location']=$hrmsEmployee['LocationString'];
					$data_p['department']=$hrmsEmployee['DepartmentString'];
					$data_p['designation']=!empty($hrmsEmployee['DesignationString'])?$hrmsEmployee['DesignationString']:'';					
					$data_p['updated_at']=date('Y-m-d H:i:s');
					
					$obj_model_productn = $app->load_model('hrms_employee');
					$obj_model_productn->map_fields($data_p);
					$obj_model_productn->execute("UPDATE",false,"","id='".$rs_employee[0]['id']."'");
				
				}
			}
		}
		$message=array("message"=>"Record added successfully.","msgcode"=>"0");
		$data=['template_name'=>'hrms_sync_admin','send_data_arary'=>['message'=>'Employee Sync Successfully.'],'subject'=>'Employee Sync Successfully.','mail_for'=>'Admin'];
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