<?
define("VIR_DIR", "scripts/autofiles/");
include("../../core/app.php");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

$app = &app::get_instance();
$app->initialize();

$from_date = $app->getGetVar('fromDate');
$to_date = $app->getGetVar('toDate');
$status = $app->getGetVar('Status');

$url = 'https://api.pockethrms.com/api/EmployeeMaster/ExpenseHRReports?Status=' . $status . '&fromDate=' . $from_date . '&toDate=' . $to_date;

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
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
$response = json_decode($response, true);

if (count($response) > 0) {
	foreach ($response as $expense) {
		$obj_model_employee = $app->load_model('hrms_employee_expenses');
		$rs_expense = $obj_model_employee->execute("SELECT", false, "", "hrms_expense_id='" . $expense['ExpenseId'] . "'");
		if (count($rs_expense) > 0) 
		{
			$data_p = array();
			$data_p['hrms_expense_id'] = $expense['ExpenseId'];
			$data_p['category'] = $expense['Category Name'];
			$data_p['expense_type'] = $expense['Expense Type'];
			$data_p['approve_amount'] = $expense['Approved Amount'];
			$data_p['finance_amount'] = $expense['Finance Amount'];
			$data_p['total_km'] = $expense['Total_KM'];
			$data_p['finance_approved'] = $expense['Finance Approved Date'];
			$data_p['trans_no'] = $expense['Trans.No.'];
			$data_p['vendor_name'] = $expense['Vendor Name'];
			$data_p['bill_no'] = $expense['Bill/Invoice No.'];
			$data_p['mode'] = $expense['Mode'];
			$data_p['status'] = $expense['Status'];
			$data_p['purpose'] = $expense['Purpose'];
			$data_p['remark'] = $expense['Employee Remark'];
			$data_p['manager1'] = $expense['Manager1'];
			$data_p['manager2'] = $expense['Manager2'];
			$data_p['manager3'] = $expense['Manager3'];
			$data_p['expense_date'] = $expense['Expense Date'];
			$data_p['created_at'] = date('Y-m-d H:i:s');
			$obj_model_expense = $app->load_model('hrms_employee_expenses');
			$obj_model_expense->map_fields($data_p);
			$obj_model_expense->execute("UPDATE", false, "", "hrms_expense_id='" . $expense['ExpenseId'] . "'");
		} 
		else
		{
			$obj_model_employee = $app->load_model('employee');
			$rs_employee = $obj_model_employee->execute("SELECT", false, "", "lms_employee_code='" . $expense['Code'] . "'");
			if (count($rs_employee) > 0) {
				$data_p = array();
				$data_p['employee_id'] = $rs_employee[0]['id'];
				$data_p['hrms_expense_id'] = $expense['ExpenseId'];
				$data_p['category'] = $expense['Category Name'];
				$data_p['expense_type'] = $expense['Expense Type'];
				$data_p['approve_amount'] = $expense['Approved Amount'];
				$data_p['finance_amount'] = $expense['Finance Amount'];
				$data_p['total_km'] = $expense['Total_KM'];
				$data_p['finance_approved'] = $expense['Finance Approved Date'];
				$data_p['trans_no'] = $expense['Trans.No.'];
				$data_p['vendor_name'] = $expense['Vendor Name'];
				$data_p['bill_no'] = $expense['Bill/Invoice No.'];
				$data_p['mode'] = $expense['Mode'];
				$data_p['status'] = $expense['Status'];
				$data_p['purpose'] = $expense['Purpose'];
				$data_p['remark'] = $expense['Employee Remark'];
				$data_p['manager1'] = $expense['Manager1'];
				$data_p['manager2'] = $expense['Manager2'];
				$data_p['manager3'] = $expense['Manager3'];
				$data_p['expense_date'] = $expense['Expense Date'];
				$data_p['created_at'] = date('Y-m-d H:i:s');

				$obj_model_expense = $app->load_model('hrms_employee_expenses');
				$obj_model_expense->map_fields($data_p);
				$obj_model_expense->execute("INSERT", false, "", "");
			}
		}
	}
	$message = array("message" => "Record added successfully.", "msgcode" => "0");
	$data=['template_name'=>'hrms_sync_admin','send_data_arary'=>['message'=>'Employee Expense Sync Successfully.'],'subject'=>'Employee Expense Sync Successfully.','mail_for'=>'Admin'];
	$app->utility->sendMial($data);
} else {
	$message = array("message" => "Record Not Found.", "msgcode" => "1");
}
$response = $message;
$opt = json_encode($response, JSON_UNESCAPED_UNICODE);
$final_response = $app->utility->indent($opt);
echo $final_response;
exit;
$app->unload();
