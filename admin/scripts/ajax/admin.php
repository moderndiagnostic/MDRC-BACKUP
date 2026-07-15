<?php
$json_class = $app->load_module("JSON");
$obj_json = new $json_class(JSON_LOOSE_TYPE);
//get action
$actionType=$app->getPostVar("actionType");
//Function for admin forget password
if($actionType=="adminForgotPass")
{
	//admin forget password
	$forgotEmail=$app->getPostVar("forgotEmail");
	if($forgotEmail!='')
	{
		$obj_model_user = $app->load_model("admin");
		$admin= $obj_model_user->execute("SELECT",false,"","(email='".$forgotEmail."') and isactive=1");
		if(count($admin)>0)
		{
			$phone=$admin[0]['phone'];
			$pass=$admin[0]['login_password'];
			$name=$admin[0]['name'];
			$email=$admin[0]['email'];
			$type='Admin';
			$subject = "Admin Login Detail";
			$to=$email;
			$header=$app->utility->web_mail_header();
			$footer=$app->utility->web_mail_footer();
			$obj_mailer = $app->load_module("mailer\sender");
			$mail_body = $app->utility->ParseMailTemplate("admin_forgot_password.html", array("email"=>$email,"name"=>$name,"password"=>$pass,"server_root"=>SERVER_ROOT));
			if($mail_body==NULL)
			{
				$app->display_error(NULL, "Could not parse the mail template");
			}
			$obj_mailer->create();
			$obj_mailer->subject($subject);
			$obj_mailer->add_to($email);
			$obj_mailer->htmlbody($mail_body);
			$flag = $obj_mailer->send();
			$msg='Password sent to Your Email Address.';
			$msgcode='0';
		}
		else
		{
			//email not found
			$msg="We couldn't find a user with that email address.";
			$msgcode='1';
		}
	}
	else
	{
		//email is blank
		$msg="We couldn't find a user with that email address.";
		$msgcode='1';
	}
}
//Function for admin login
if($actionType=="adminLogin")
{
	$loginEmail=$app->getPostVar("loginEmail");
	$loginPass=$app->getPostVar("loginPass");
	$loginRemember=$app->getPostVar("loginRemember");
	if($loginEmail!='' && $loginPass!='')
	{
		$obj_model_admin = $app->load_model("employee");
		$rsUser = $obj_model_admin->execute("SELECT",false,"","(email='".$loginEmail."' or mobile='".$loginEmail."' or lms_employee_code='".$loginEmail."') and login_password='".$loginPass."' and status='Active'");
		if(count($rsUser)==1)
		{
			$_SESSION['employeeId'] = $rsUser[0]['id'];
			$_SESSION['employeeRole'] = $rsUser[0]['employee_role'];
			$_SESSION['employeeName'] = $rsUser[0]['name'];
			$_SESSION['employeeDesignation'] = $rsUser[0]['designation'];
			//set cookie
			if($loginRemember!='')
			{
				setcookie("MDRCSALES", $loginEmail, time() + 3600, '/');
			}

			$update_field1 = array();
			$update_field1['employee_id'] = $rsUser[0]['id'];
			$update_field1['title'] =$_SESSION['employeeName'].' Login';
			$update_field1['activity_desc'] ='';
			$update_field1['ip'] = $_SERVER['REMOTE_ADDR'];
			$update_field1['updated_at']=date("Y-m-d H:i:s");
			$obj_model_admin_logins= $app->load_model("employee_activity");
			$obj_model_admin_logins->map_fields($update_field1);
			$obj_model_admin_logins->execute("INSERT", false, "", "");

			$url=$rsUser[0]['employee_role']=='Admin'?'index.php?view=home':'index.php?view=employee_dashboard';
			$msg='Login Sucessfully.';
			$msgcode='0';
		}
		else
		{
			//email is blank
			$msg="Please enter correct login details.";
			$msgcode='1';
		}
	}
	else
	{
		//email is blank
		$msg="We couldn't find a user with that email address.";
		$msgcode='1';
	}
}
//Function for admin logout
if($actionType=="adminLogout")
{
	unset($_SESSION['employeeId']);
	unset($_SESSION['employeeName']);
	unset($_SESSION['employeeDesignation']);
	unset($_SESSION['employeeRole']);
	unset($_COOKIE['MDRCSALES']);
	setcookie("MDRCSALES", '', time() - 3600, '/');
	$msg='Logout Sucessfully.';
	$msgcode='0';
}
//if action blank
if($actionType=="")
{
	$msg="We couldn't find any valid call.";
	$msgcode='1';
}
echo $obj_json->encode(array("RESULT"=>$msgcode,"url"=>$url??"","msg"=>$msg));
?>