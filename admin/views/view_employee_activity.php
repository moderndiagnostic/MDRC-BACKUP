<!-- vendor css -->
<link href="lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
<link href="lib/typicons.font/typicons.css" rel="stylesheet">
<link href="lib/prismjs/themes/prism-vs.css" rel="stylesheet">

<link href="lib/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="lib/select2/css/select2.min.css" rel="stylesheet">

<!-- DashForge CSS -->
<link rel="stylesheet" href="assets/css/dashforge.css">
<link rel="stylesheet" href="assets/css/dashforge.demo.css">

<!-- Skin CSS -->
<link rel="stylesheet" href="assets/css/skin.cool.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/custom.css">

<!-- file upload  -->
<link href="lib/bootstrap-file/css/fileupload.css" rel="stylesheet" type="text/css" />
<!--Sweet Alert CSS & JS -->
<link href="lib/alert/css/sweet-alert.css" rel="stylesheet" type="text/css" />

<!--image popup -->
<link href="lib/magnific-popup/css/magnific-popup.css" rel="stylesheet" type="text/css" />
<style>
.ui-autocomplete.ui-autocomplete.ui-front{
  z-index: 999999;
}
</style>

<?php
if ($_SESSION['employeeRole'] == 'Admin') {
  include('includes/menu.php');
} else {
  include('includes/employee_menu_sidebar.php');
}
?>

<div class="content ht-100v pd-0">
  <?php include('includes/header.php'); ?>

  <div class="content-body">
    <div class="container">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <h3 class="mg-b-0 tx-spacing--1">Login History</h3>
        </div>
        <div class="">
          <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5" onclick="mulitple_employee_activity_select();"><i data-feather="trash" class="wd-10 mg-r-5"></i> Delete</button>
        </div>
      </div>
      <div class="df-example demo-table">
        <table id="table_employee_activity" class="table">
          <thead>
            <tr>
              <th class="wd-5p">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input checkAll" id="customCheck0" name="select_multiple" value="Yes">
                  <label class="custom-control-label" for="customCheck0"></label>
                </div>
              </th>
              <th class="wd-5p">ID.</th>
              <th class="wd-15p">Employee Name</th>
              <th class="wd-15p">title</th>
              <th class="wd-10p">Ip</th>
              <th class="wd-10p">Date</th>
              <th class="wd-5p">Action</th>
            </tr>
          </thead>
        </table>
      </div>
      <!-- df-example -->

      <?php include('includes/footer.php'); ?>
      <!-- content-footer -->

    </div>
    <!-- container -->
  </div>
</div>
<!-- content -->

<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="lib/feather-icons/feather.min.js"></script>
<script src="lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="lib/prismjs/prism.js"></script>
<script src="lib/parsleyjs/parsley.min.js"></script>

<script src="lib/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="lib/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
<script src="lib/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>

<script src="lib/select2/js/select2.min.js"></script>
<script src="assets/js/dashforge.aside.js"></script>
<script src="assets/js/dashforge.js"></script>
<!-- file upload  -->
<script src="lib/bootstrap-file/js/fileupload.js"></script>
<!-- image popup -->
<link href="lib/magnific-popup/css/magnific-popup.css" rel="stylesheet" type="text/css" />
<script src="lib/magnific-popup/js/jquery.magnific-popup.js"></script>

<!-- other include -->
<script src="lib/alert/js/sweet-alert.min.js"></script>
<script src="lib/alert/js/jquery.sweet-alert.init.js"></script>
<script src="lib/validate/js/jquery.validate.min.js"></script>

<script>
$(function(){
'use strict'
	$('.input-datepicker').datepicker({dateFormat: 'dd-mm-yy'});
});
$('.select2').select2({});
</script>
<script src="lib/jqueryui/jquery-ui.min.js"></script>

<link href='lib/selectdropdown/jquery-ui.min.css' rel='stylesheet' type='text/css'>

<!-- Custom -->
<script src="scripts/js/grocery.js"></script>
<script src="scripts/js/employee_activity.js"></script>