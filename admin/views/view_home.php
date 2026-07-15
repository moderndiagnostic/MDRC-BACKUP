<!-- vendor css -->
<link href="lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
<link href="lib/jqvmap/jqvmap.min.css" rel="stylesheet">
<link href="lib/morris.js/morris.css" rel="stylesheet">
<link href="lib/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
<!-- DashForge CSS -->
<link rel="stylesheet" href="assets/css/dashforge.css">
<link rel="stylesheet" href="assets/css/dashforge.dashboard.css">
<!-- Skin CSS -->
<link rel="stylesheet" href="assets/css/skin.cool.css">
<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/custom.css">
<!--Sweet Alert CSS & JS -->
<link href="lib/alert/css/sweet-alert.css" rel="stylesheet" type="text/css" />

<?php 
if($_SESSION['employeeRole']=='Admin')
{
  include('includes/menu.php'); 
}
else
{
  include('includes/employee_menu_sidebar.php'); 
}
?>
<div class="content ht-100v pd-0">
  <?php include('includes/header.php'); ?>
  <!-- content-header -->
  <div class="content-body">
    <div class="container pd-x-0">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <h4 class="mg-b-0 tx-spacing--1">Welcome to Dashboard</h4>
        </div>
        <div class="d-none d-md-block"> <a style="display:none" href="index.php?view=email_data_list" class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="mail" class="wd-10 mg-r-5"></i> Email Settings</a> <a style="display:none" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5" href="index.php?view=general_settings"><i data-feather="settings" class="wd-10 mg-r-5"></i> Generate Settigs</a> </div>
      </div>
      <?= $this->utility->get_message(); ?>
      <div class="row row-xs">
        <div class="col-sm-12 col-lg-12">
          <div class="row row-xs">

            <div class="col-sm-6 col-lg-6 mg-b-10 mg-t-10"> <a href="index.php?view=project_list&product_status=Residential">
                <div class="card card-body">
                  <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Employee</h6>
                  <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <?php $total_csr = $this->total_csr; ?>
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">
                      <?= $total_csr ?>
                    </h3>
                  </div>
                </div>
              </a>
            </div>
            <!-- col -->

            <div class="col-sm-6 col-lg-6 mg-t-10 mg-b-10"> <a href="index.php?view=project_list&product_status=Commercial">
                <div class="card card-body">
                  <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Client</h6>
                  <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <?php $total_c = $this->total_c; ?>
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">
                      <?= $total_c; ?>
                    </h3>
                  </div>
                </div>
              </a>
            </div>
            <!-- col -->

          </div>
        </div>
      </div>


      <!-- row -->
    </div>
    <!-- container -->
  </div>
</div>
<div class="custom_ajax_preloader" id="custom_ajax_preloader" style="display: none;"> <span> <img src="assets/img/loader/ajax-loader.gif"> </span> </div>
<!-- Ajax modal container-->
<div class="ajax_modal_container" id="ajax_modal_container"> </div>
<!-- content-footer -->
</div>
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="lib/feather-icons/feather.min.js"></script>
<script src="lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="lib/jquery.flot/jquery.flot.js"></script>
<script src="lib/jquery.flot/jquery.flot.stack.js"></script>
<script src="lib/jquery.flot/jquery.flot.resize.js"></script>
<script src="lib/chart.js/Chart.bundle.min.js"></script>
<script src="lib/jqvmap/jquery.vmap.min.js"></script>
<script src="lib/jqvmap/maps/jquery.vmap.usa.js"></script>
<script src="lib/parsleyjs/parsley.min.js"></script>
<script src="lib/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="lib/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
<script src="lib/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
<script src="assets/js/dashforge.js"></script>
<script src="assets/js/dashforge.aside.js"></script>
<script src="assets/js/dashforge.sampledata.js"></script>
<!-- other include -->
<script src="lib/alert/js/sweet-alert.min.js"></script>
<script src="lib/alert/js/jquery.sweet-alert.init.js"></script>
<script src="lib/validate/js/jquery.validate.min.js"></script>
<!-- Custom -->
<script src="scripts/js/grocery.js"></script>
<script src="scripts/js/admin.js"></script>

<script src="lib/raphael/raphael.min.js"></script>
<script src="lib/morris.js/morris.min.js"></script>
<script src="lib/jqueryui/jquery-ui.min.js"></script>