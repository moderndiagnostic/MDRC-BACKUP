<link href="lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
<link href="lib/typicons.font/typicons.css" rel="stylesheet">
<link href="lib/prismjs/themes/prism-vs.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/dashforge.auth.css">
<link href="lib/select2/css/select2.min.css" rel="stylesheet">
<!-- DashForge CSS -->
<link rel="stylesheet" href="assets/css/dashforge.css">
<link rel="stylesheet" href="assets/css/dashforge.demo.css">
<!-- Skin CSS -->
<link rel="stylesheet" href="assets/css/skin.cool.css">
<!--<link rel="stylesheet" href="assets/css/skin.charcoal.css">-->
<!--Sweet Alert CSS & JS -->
<link href="lib/alert/css/sweet-alert.css" rel="stylesheet" type="text/css" />
<!-- file upload  -->
<link href="lib/bootstrap-file/css/fileupload.css" rel="stylesheet" type="text/css" />
<!--image popup -->
<link href="lib/magnific-popup/css/magnific-popup.css" rel="stylesheet" type="text/css" />
<!-- new added by developer -->
<link rel="stylesheet" href="assets/css/custom.css">
<style>
  .scrollbox {
    overflow-y: scroll;
    max-height: 220px;
    border: 1px solid #dae0e8;
  }

  .even {
    margin-left: 20px;
  }
</style>
<?php include('includes/menu.php'); ?>
<div class="content ht-100v pd-0">
  <?php include('includes/header.php'); ?>
  <!-- content-header -->
  <div class="content-body">
    <div class="container pd-x-0">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>

          <h4 class="mg-b-0 tx-spacing--1">
            <?= $this->to_do ?>
            <?= $this->manage_for ?>
          </h4>
        </div>

      </div>


      <div class="row row-xs">

        <div class="col-sm-4 col-lg-4 mg-b-10 mg-t-10"> <a href="javascript:void(0);" class="sync_hrml" data-type="employee">
            <div class="card card-body">
              <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Sync Employee</h6>
            </div>
          </a>
        </div>
        <!-- col -->

        <div class="col-sm-4 col-lg-4 mg-t-10 mg-b-10"> <a href="javascript:void(0);" class="sync_hrml" data-type="expense">
            <div class="card card-body">
              <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Sync Expense</h6>

            </div>
          </a>
        </div>

        <div class="col-sm-4 col-lg-4 mg-t-10 mg-b-10"> <a href="javascript:void(0);" class="sync_hrml" data-type="salary">
            <div class="card card-body">
              <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Sync Salary</h6>
            </div>
          </a>
        </div>
        <!-- col -->

      </div>


      <?php include('includes/footer.php'); ?>
    </div>
    <!-- container -->
  </div>
</div>
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="lib/feather-icons/feather.min.js"></script>
<script src="lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="lib/prismjs/prism.js"></script>
<script src="lib/parsleyjs/parsley.min.js"></script>
<script src="lib/select2/js/select2.min.js"></script>

<script src="assets/js/dashforge.aside.js"></script>
<script src="assets/js/dashforge.js"></script>
<!-- other include -->
<script src="lib/alert/js/sweet-alert.min.js"></script>
<script src="lib/alert/js/jquery.sweet-alert.init.js"></script>
<!-- file upload  -->
<script src="lib/bootstrap-file/js/fileupload.js"></script>
<!-- image popup -->
<script src="lib/magnific-popup/js/jquery.magnific-popup.js"></script>
<script src="lib/validate/js/jquery.validate.min.js"></script>
<!-- Custom -->
<script src="scripts/js/grocery.js"></script>

<script src="lib/editor/ckeditor/ckeditor.js"></script>

<script>
  $(document).on("click", ".sync_hrml", function() {
     var type = $(this).data("type");
    swal({
      title: "Are you sure?",
      text: "You will not be able to undo after this action!",
      type: "warning",
      showCancelButton: true,
      cancelButtonClass: 'btn-primary',
      confirmButtonClass: 'btn-warning',
      confirmButtonText: "Yes, Sync it!",
      confirmButtonClass: "confirm btn btn-lg btn-warning xyz",
      closeOnConfirm: true
    }, function(r) {
      if (r == true) {
        if (type == 'employee') {
          var url = 'syncHrmsEmployee.php';
        } else if (type == 'expense') {
          var url = 'syncHrmsEmployeeExpense.php';
        } else {
          var url = 'syncHrmsEmployeeSalary.php';
        }

        $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>Data Sync Started.</p>', {
          type: 'warning',
          delay: 3000,
          allow_dismiss: true,
          offset: {
            from: 'top',
            amount: 20
          }
        });
        fetch("https://www.mdrcindia.com/SalesApp/scripts/hrms/" + url);
        return false;
      } else {
        return false;
      }
    });
  });
</script>