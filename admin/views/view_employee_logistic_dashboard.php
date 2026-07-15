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
if ($_SESSION['employeeRole'] == 'Admin') {
  include('includes/menu.php');
} else {
  include('includes/employee_menu_sidebar.php');
}
?>
<div class="content ht-100v pd-0">
  <?php include('includes/header.php'); ?>
  <!-- content-header -->
  <div class="content-body">
    <div class="pd-x-0">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <h4 class="mg-b-0 tx-spacing--1">Logistic Sample Collection Dashboard</h4>
        </div>
        <div class="d-md-block"> <a href="javascript:void(0)" data-url="index.php?view=employee_logistic_dashboard&act=export_data" class="btn btn-sm pd-x-15 btn-warning btn-uppercase export"><i data-feather="download" class="wd-10 mg-r-5"></i>Export Data</a></div>
      </div>
      <?= $this->utility->get_message(); ?>
      <div class="row row-xs">
        <div class="col-sm-12 col-lg-12">
          <div class="row row-xs">
            <div class="col-sm-3 col-lg-3 mg-b-10">
              <div class="card">
                <div class="card-header">
                  <div class="contact-list">
                    <div class="media active">
                      <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-circle bg-gray-700">A</span>
                      </div>
                      <div class="media-body mg-l-10">
                        <h6 class="tx-13 mg-b-3"><?= $this->employee['name'] ?></h6>
                        <span class="tx-12"><?= $this->employee['mobile'] ?></span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="contact-sidebar-body ps">
                    <div class="d-flex align-items-center justify-content-between mg-b-20">
                      <h6 class="tx-uppercase tx-semibold mg-b-0">My Teams</h6>
                    </div>
                    <input type="search" id="searchEmployee" placeholder="Search employee..." class="form-control mb-3" />
                    <div class="row">
                      <div class="col-12" style="max-height: 800px;overflow-y: auto;">
                        <!-- all employee html -->
                        <?= $this->html ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-9 col-lg-9">
              <div class="card card-body mg-b-10">
                <div data-label="Search" class="df-example demo-table">
                  <? $this->htmlBuilder->buildTag("form", array("action" => "", "method" => "post", "autocomplete" => "off", "class" => "form-validate", "data-parsley-validate" => ""), "frm_search"); ?>
                  <div class="row">
                    <div class="form-group col-md-6 start_dates">
                      <label>Created Start Date</label>
                      <? $this->htmlBuilder->buildTag("input", array("type" => "text", "class" => "form-control start_date input-datepicker", "id" => "start_date", "name" => "start_date", "placeholder" => "Start Date ", "data-date-format" => "mm/dd/yyyy",'value'=>date('01-m-Y')), "") ?>
                    </div>
                    <div class="form-group col-md-6 end_dates">
                      <label>Created End Date</label>
                      <? $this->htmlBuilder->buildTag("input", array("type" => "text", "class" => "form-control end_date input-datepicker", "id" => "end_date", "name" => "end_date", "placeholder" => "End Date", "data-date-format" => "mm/dd/yyyy",'value'=>date('t-m-Y')), "") ?>
                    </div>
                    <div class="form-group col-md-12">
                      <button type="button" class="btn btn-success search_button" onclick="search_data()">Search</button>
                      <button type="button" class="btn btn-danger" onclick="reset_data()">Reset</button>
                    </div>
                  </div>
                  <?= $this->htmlBuilder->closeForm() ?>
                </div>
              </div>
              <div class="">
                <div class="row align-items-sm-end">
                  <div class="col-12">
                    <div class="row g-3 mb-3 ">
                      <div class="col-sm-12 col-lg-12 mg-t-10 mg-b-10">
                        <div class="row taskHtml3">
                          <!-- append html here -->
                        </div>
                      </div>
                      <div class="col-sm-12 col-lg-12 mg-t-10 mg-b-10 selectedEmployeeCard" style="display:none">
                        <div class="card">
                          <div class="card-header">
                            <div class="tx-16 d-flex justify-content-between align-items-center">
                              <h5 class="mg-b-0 text-green fw-bold selectedEmployeeName"></h5>
                              <button type="button" class="btn-close" onclick="closeSubEmployee()">
                                  <span aria-hidden="true">X</span>
                              </button>
                            </div>
                          </div>
                          <div class="card-body shadow-sm p-4">
                            <div class="row align-items-center">
                              <!-- Chart Section -->
                              <div class="col-md-4">
                                <div class="chart-seven" style="width: 100%; height: 280px;">
                                  <canvas class="w-100 h-100" id="chartDonut3"></canvas>
                                </div>
                              </div>
                              <!-- Task Data Section -->
                              <div class="col-md-8">
                                <div class="row taskHtml4">
                                  <!-- append html here -->
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-lg-12 mg-t-10 mg-b-10">
                        <div class="card">
                          <div class="card-header">
                            <div class="tx-16 d-flex justify-content-between align-items-center">
                              <h5 class="mg-b-0 text-green fw-bold"><?= $this->employee['name'] ?> Visits</h5>
                            </div>
                          </div>
                          <div class="card-body shadow-sm p-4">
                            <div class="row align-items-center">
                              <!-- Chart Section -->
                              <div class="col-md-4">
                                <div class="chart-seven" style="width: 100%; height: 280px;">
                                  <canvas class="w-100 h-100" id="chartDonut"></canvas>
                                </div>
                              </div>
                              <!-- Task Data Section -->
                              <div class="col-md-8">
                                <div class="row taskHtml1">
                                  <!-- append html here -->
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-lg-12 mg-t-10 mg-b-10">
                        <div class="card">
                          <div class="card-header">
                            <div class="tx-16 d-flex justify-content-between align-items-center">
                              <h5 class="mg-b-0 text-green fw-bold">All Team Visits</h5>
                            </div>
                          </div>
                          <div class="card-body shadow-sm p-4">
                            <div class="row align-items-center">
                              <!-- Chart Section -->
                              <div class="col-md-4">
                                <div class="chart-seven" style="width: 100%; height: 280px;">
                                  <canvas class="w-100 h-100" id="chartDonut2"></canvas>
                                </div>
                              </div>
                              <!-- Task Data Section -->
                              <div class="col-md-8">
                                <div class="row taskHtml2">
                                  <!-- append html here -->
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
<script src="scripts/js/employee_logistic_dashboard.js<?= ALLVERSION ?>"></script>
<script>
  $(function() {
    'use strict'
    $('.input-datepicker').datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
</script>
<script type="text/javascript">
    function toggleSubEmployees(element) {
    // Find the next sibling <ul> containing the sub-employees
    var subList = element.closest('li').querySelector('ul.subEmployee');
    
    if (subList) {
        // Toggle the visibility of the sublist
        if (subList.style.display === 'none' || subList.style.display === '') {
            subList.style.display = 'block';  // Show the sublist
            element.innerHTML = '<i class="fas fa-chevron-up"></i>';  // Change button icon to down arrow
        } else {
            subList.style.display = 'none';  // Hide the sublist
            element.innerHTML = '<i class="fas fa-chevron-down"></i>';  // Change button icon to up arrow
        }
    }
}
$('.export').on('click', function () {
    var url = $(this).data('url');
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    $(this).attr('href', url + '&start_date=' + start_date + '&end_date=' + end_date);
});
</script>
<script>
  $(document).on("input propertychange paste change keyup", '#searchEmployee', function (e) {
  var value = $(this).val().toLowerCase();
  if (value === '') {
    $('.employeeList.subEmployee').slideUp();
    $('.toggle-btn i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    $('.cat-search').closest('li').show();
    return;
  }
  $('.cat-search').each(function () {
    var $this = $(this);
    var text = $this.text().toLowerCase();
    var isMatch = text.indexOf(value) > -1;
    if (isMatch) {
      $this.closest('li').show();
      $this.parents('ul.subEmployee').show();
      $this.parents('li').show();
      $this.parents('li').children('h6').find('.toggle-btn i')
        .removeClass('fa-chevron-down')
        .addClass('fa-chevron-up');
    } else {
      $this.closest('li').hide();
    }
  });
});
</script>