<div class="content-header">
   <div class="content-search"> <!--<i data-feather="search"></i>
      <input type="search" class="form-control" placeholder="Search...">-->
    </div>
    <nav class="nav"> 
    <!-- <a onclick="userLogout()" class="nav-link userLogout"><i data-feather="log-out"></i> <span>Sign Out</span></a> -->
    
    <div class="dropdown dropdown-profile">
          <a href="" class="dropdown-link" data-toggle="dropdown" data-display="static">
            <div class="avatar avatar-sm"><img src="assets/img/profile.png" class="rounded-circle" alt=""></div>
          </a><!-- dropdown-link -->
          <div class="dropdown-menu dropdown-menu-right tx-13">
            <div class="avatar avatar-lg mg-b-15"><img src="assets/img/profile.png" class="rounded-circle" alt=""></div>
            <h6 class="tx-semibold mg-b-5"><?=$_SESSION['employeeName'];?></h6>
            <p class="mg-b-25 tx-12 tx-color-03"><?=$_SESSION['employeeDesignation'];?></p>

            <a href="index.php?view=account_setting" class="dropdown-item"><i data-feather="edit-3"></i> Change Password</a>
            <a href="index.php?view=employee_activity" class="dropdown-item"><i data-feather="settings"></i>Login History</a>
            <a onclick="userLogout()" class="dropdown-item userLogout"><i data-feather="log-out"></i>Sign Out</a>
          </div><!-- dropdown-menu -->
        </div><!-- dropdown -->

    <!--<a href="" class="nav-link"><i data-feather="align-left"></i></a>--> </nav>
  </div>