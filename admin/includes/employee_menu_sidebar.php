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
    <ul class="nav nav-aside sidebar-nav">
      <li class="nav-label">Summary</li>
      <li class="nav-item mysub" data-main="employee_dashboard"><a href="index.php?view=employee_dashboard" class="nav-link"><i data-feather="monitor"></i> <span>Dashboard</span></a></li>   
      <li class="nav-item mysub" data-main="employee_logistic_dashboard"><a href="index.php?view=employee_logistic_dashboard" class="nav-link"><i data-feather="monitor"></i> <span>Logistic Dashboard</span></a></li>   
    </ul>
  </div>
</aside>