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

<!--Sweet Alert CSS & JS -->
<link href="lib/alert/css/sweet-alert.css" rel="stylesheet" type="text/css" />
<?php include('includes/menu.php');?>

<div class="content ht-100v pd-0">
  <?php include('includes/header.php');?>
    
  <div class="content-body">
    <div class="container">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <h3 class="mg-b-0 tx-spacing--1">Payments</h3>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-warning btn-uppercase mg-l-5 employee_sample_pickup_payment_export" href="javascript:void(0)"><i data-feather="download" class="wd-10 mg-r-5"></i> Export</a>
        </div>
      </div>
      <div data-label="Search" class="df-example demo-table" >
        <? $this->htmlBuilder->buildTag("form", array("action"=>"","method"=>"post","autocomplete"=>"off","class"=>"form-validate","data-parsley-validate"=>""), "frm_search");?>
        <div class="row">
          <div class="form-group col-md-4 start_dates" >
            <label>Transaction Start Date</label>
            <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control start_date input-datepicker","id"=>"start_date","name"=>"start_date","placeholder"=>"Start Date ","data-date-format"=>"mm/dd/yyyy"), "") ?>
          </div>
          <div class="form-group col-md-4 end_dates" >
            <label>Transaction End Date</label>
            <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control end_date input-datepicker","id"=>"end_date","name"=>"end_date","placeholder"=>"End Date","data-date-format"=>"mm/dd/yyyy"), "") ?>
          </div>
          <div class="form-group col-md-4">
            <label>Select Employee</label>
            <? $this->htmlBuilder->buildTag("select", array("type"=>"text","class"=>"form-control select2" ,"values"=>$this->employee), "employee_id") ?>
          </div>
          <div class="form-group col-md-12" >
            <button type="button" class="btn btn-success search_button" onclick="search_data()">Search</button>
            <button type="button" class="btn btn-danger"  onclick="reset_data()">Reset</button>
          </div>
        </div>
        <?=$this->htmlBuilder->closeForm()?>
      </div>

      <div class="df-example datatable-menu-tab">
        <ul class="nav justify-content-left">
          <li class="nav-item"> <a class="nav-link tab_link active" href="javascript:void(0)">All</a></li>
          <li class="nav-item"> <a class="nav-link tab_link" href="javascript:void(0)">Success</a></li>
          <li class="nav-item"> <a class="nav-link tab_link" href="javascript:void(0)">Fail</a></li>
          <input type="hidden" name="tab_filter" id="tab_filter" value="">
        </ul>
      </div>

      <!--<p class="mg-b-30">Sytem accept only order of below citys.</p>-->
      <div class="df-example demo-table">
        <table id="table_payment_link" class="table">
          <thead>
            <tr>
              <th>ID.</th>
              <th>Client Name</th>
              <th>Employee Name</th>
              <th>Amount</th>
              <th>Transaction</th>
              <th>Status</th>
              <!-- <th>Action</th> -->
            </tr>
          </thead>
        </table>
      </div>
      <!-- df-example -->
      
      <?php include('includes/footer.php');?>
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
<!-- Custom --> 
<script src="scripts/js/grocery.js"></script> 
<script src="scripts/js/employee_sample_pickup_payment.js"></script> 
<script src="lib/jqueryui/jquery-ui.min.js"></script>