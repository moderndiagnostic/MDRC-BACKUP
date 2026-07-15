<style>
.aside-body 
{
    height: calc(100%) !important;
}
.content.ht-100v.pd-0
{
	height: auto !important;
}
</style>
<aside class="aside aside-fixed">
  <div class="aside-header"> <a href="index.php" class="aside-logo"><img src="assets/img/logo.svg" class="w-75" alt="MDRC">  </a> <a href="" class="aside-menu-link"> <i data-feather="menu"></i> <i data-feather="x"></i> </a> </div>
  <div class="aside-body">
    <div class="aside-loggedin">
      <div class="d-flex align-items-center justify-content-start"> <a href="" class="avatar"><img src="assets/img/profile.png" class="rounded-circle" alt=""></a>
        <div class="aside-alert-link">
          <a onclick="userLogout()" data-toggle="tooltip" title="Sign out" class="userLogout"><i data-feather="log-out"></i></a>
        </div>
      </div>
      <div class="aside-loggedin-user"> <a href="#loggedinMenu" class="d-flex align-items-center justify-content-between mg-b-2" data-toggle="collapse">
        <h6 class="tx-semibold mg-b-0">
          <?=$_SESSION['employeeName'];?>
        </h6>
        <i data-feather="chevron-down"></i> </a>
        <p class="tx-color-03 tx-12 mg-b-0"><?=$_SESSION['employeeDesignation'];?></p>
      </div>
      <div class="collapse" id="loggedinMenu">
        <ul class="nav nav-aside mg-b-0">
          <li class="nav-item"><a href="index.php?view=account_setting" class="nav-link"><i data-feather="edit"></i> <span>Edit Profile</span></a></li>
          <li class="nav-item"><a onclick="userLogout()" href="javascript:void(0)" class="nav-link userLogout"><i data-feather="log-out"></i> <span>Sign Out</span></a></li>
        </ul>
      </div>
    </div>
    
    <!-- aside-loggedin -->
    <ul class="nav nav-aside sidebar-nav">
      <li class="nav-label">Summary</li>
      <li class="nav-item mysub" data-main="home"><a href="index.php?view=home" class="nav-link"><i data-feather="monitor"></i> <span>Dashboard</span></a></li>
      
      <li class="nav-label mg-t-25">Client / Field Visit</li>
      <li class="nav-item with-sub mysub" data-main="client_list,client_addedit,client_detail"> <a href="" class="nav-link"><i data-feather="server"></i> <span>Clients</span></a>
        <ul class="sub_main1">
          <li  data-sub="client_list" class=""><a href="index.php?view=client_list">Client / Field Visit</a></li>
          <li  data-sub="client_addedit" class=""><a href="index.php?view=client_addedit">Add Client / Field Visit</a></li>
        </ul>
      </li>
      <li class="nav-item with-sub mysub" data-main="client_addedit,client_logistic_assign_list"> <a href="" class="nav-link"><i data-feather="file-text"></i> <span>Assign Logistic</span></a>
        <ul class="sub_main1">
          <li  data-sub="client_logistic_assign_list" class=""><a href="index.php?view=client_logistic_assign_list">Client Logistic </a></li>
        </ul>
      </li>

      <li class="nav-label mg-t-25">Logistic</li>
      <li class="nav-item with-sub mysub" data-main="employee_sample_pickup_list,employee_sample_pickup_payment_list"> <a href="" class="nav-link"><i data-feather="archive"></i> <span>Sample Pickup</span></a>
        <ul class="sub_main1">
          <li  data-sub="employee_sample_pickup_list" class=""><a href="index.php?view=employee_sample_pickup_list">All Pickups</a></li>
          <li  data-sub="employee_sample_pickup_payment_list" class=""><a href="index.php?view=employee_sample_pickup_payment_list">Sample Pickup Payments</a></li>
        </ul>
        <li class="nav-item with-sub mysub" data-main="employee_sample_dispatch_list"> <a href="" class="nav-link"><i data-feather="user"></i> <span>Sample Dispatch</span></a>
        <ul class="sub_main1">
          <li  data-sub="employee_sample_pickup_list" class=""><a href="index.php?view=employee_sample_dispatch_list">Sample Dispatch</a></li>
        </ul>
        <li class="nav-item mysub" data-main="employee_leave_list"><a href="index.php?view=employee_leave_list" class="nav-link"><i data-feather="user-x"></i> <span>Leaves</span></a></li>

      <li class="nav-label mg-t-25">Task</li>
      <li class="nav-item with-sub mysub" data-main="task_list,task_detail,task_office_list,task_office_detail"> <a href="javascript:void(0)" class="nav-link"><i data-feather="clipboard"></i> <span>All Task</span></a>
        <ul class="sub_main1">
          <li  data-sub="task_list" class=""><a href="index.php?view=task_list">Field Task</a></li>
          <li  data-sub="task_office_list" class=""><a href="index.php?view=task_office_list">Office Task</a></li>
        </ul>
      </li>

      <li class="nav-label mg-t-25">Logistics</li>
      <li class="nav-item with-sub mysub" data-main="journey_list,journey_list"> <a href="javascript:void(0)" class="nav-link"><i data-feather="navigation"></i> <span>Logistic Confirmation</span></a>
        <ul class="sub_main1">
          <li  data-sub="journey_list" class=""><a href="index.php?view=journey_list">List</a></li>
        </ul>
      </li>


      <li class="nav-label mg-t-25">Payment Link</li>
      <li class="nav-item with-sub mysub" data-main="payment_link_list"> <a href="javascript:void(0)" class="nav-link"><i data-feather="link"></i> <span>Payment Links</span></a>
        <ul class="sub_main1">
          <li  data-sub="payment_link_list" class=""><a href="index.php?view=payment_link_list">Link List</a></li>
          
        </ul>
      </li>

      <li class="nav-label mg-t-25">HRMS</li>
      <li class="nav-item with-sub mysub" data-main="hrms_settings"> <a href="javascript:void(0)" class="nav-link"><i data-feather="link"></i> <span>HRMS</span></a>
        <ul class="sub_main1">
          <li  data-sub="hrms_settings" class=""><a href="index.php?view=hrms_settings">Sync Jobs</a></li>
          
        </ul>
      </li>

      <li class="nav-label mg-t-25">Employee</li>
      <li class="nav-item with-sub mysub" data-main="employee_detail,employee_list,employee_activity,master_designation_list,master_centre_list,employee_punchinout_list,employee_finance_manager_list"> <a href="javascript:void(0)" class="nav-link"><i data-feather="briefcase"></i> <span> Employee</span></a>
        <ul class="sub_main1">
          <li data-sub="employee_list" class=""><a href="index.php?view=employee_list">All Employee</a></li>
          <li data-sub="employee_activity" class=""><a href="index.php?view=employee_activity">Activity</a></li>
          <li data-sub="master_designation_list" class=""><a href="index.php?view=master_designation_list">Designation</a></li>
          <li data-sub="employee_punchinout_list" class=""><a href="index.php?view=employee_punchinout_list">Punch IN-OUT</a></li>
          <li data-sub="employee_finance_manager_list"><a href="index.php?view=employee_finance_manager_list">Finance Manager</a></li>
        </ul>
      </li>

      <li class="nav-label mg-t-25">Settings</li>
      <li class="nav-item with-sub mysub" data-main="general_settings,common_code,state_list,city_list,master_centre_list"> <a href="javascript:void(0)" class="nav-link"><i data-feather="settings"></i> <span> Common</span></a>
        <ul class="sub_main1">
          <li data-sub="state_list" class=""><a href="index.php?view=state_list">State</a></li>
          <li data-sub="city_list" class=""><a href="index.php?view=city_list">City</a></li>
          <li data-sub="master_centre_list" class=""><a href="index.php?view=master_centre_list">Centre List</a></li>
          <li data-sub="general_settings" class=""><a href="index.php?view=general_settings">General Setting</a></li>
          <li data-sub="common_code" class="d-none"><a href="index.php?view=common_code">Head/Body Code</a></li>
        </ul>
      </li>
          
    </ul>
  </div>
</aside>