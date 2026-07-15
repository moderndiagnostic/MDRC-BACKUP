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

  .price_varient {
    padding: 0;
    margin: 0;
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
  <?php
  $image = $this->utility->get_image_path($this->rscat["image"], 'employee', 'thumb');
  if ($this->rscat['image'] != '') {
    $file_class = "fileupload-exists";
  } else {
    $file_class = "fileupload-new";
  }
  ?>
  <!-- content-header -->
  <div class="content-body">
    <div class="container pd-x-0">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <h4 class="mg-b-0 tx-spacing--1">
           Change Password
          </h4>
        </div>
        <div class="d-none d-md-block"> </div>
      </div>
      <?= $this->utility->get_message() ?>
      <? $this->htmlBuilder->buildTag("form", array("action" => "", "data-parsley-validate" => "", "class" => "form-horizontal form-bordered form-validate"), "frm_profile"); ?>
      <? $this->htmlBuilder->buildTag("input", array("type" => "hidden", "value" => $this->id), "id"); ?>
      <? $this->htmlBuilder->buildTag("input", array("type" => "hidden", "value" => "update_data"), "act"); ?>
      <div class="row">
        <div class="col-lg-12">
          <div data-label="" class="df-example demo-forms">
            <div class="form-group">
              <label class="d-block">New Password <span class="tx-danger">*</span></label>
              <div class="input-group">
                <? if ($this->id != "") { ?>
                  <? $this->htmlBuilder->buildTag("input", array("type" => "password", "value" => $this->login_password, "class" => "form-control required", "required" => ""), "login_password") ?>
                <? } else { ?>
                  <? $this->htmlBuilder->buildTag("input", array("type" => "password", "class" => "form-control required", "required" => "required"), "login_password") ?>
                <?php } ?>
                <div class="input-group-prepend">
                  <div class="input-group-text"> <i toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"></i> </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="d-block">Confirm Password <span class="tx-danger">*</span></label>
              <div class="input-group mb-2">
                  <? $this->htmlBuilder->buildTag("input", array("type" => "password", "class" => "form-control required", "required" => "required"), "confirm_password") ?>
                <div class="input-group-prepend">
                  <div class="input-group-text"> <i toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password2"></i> </div>
                </div>
              </div>
              <span id="message"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="row mg-t-15">
        <div class="col-lg-12">
          <button class="btn btn-primary" type="submit">Submit</button>
        </div>
      </div>
      </form>
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
<!-- ckeditor -->

<script>
  $(document).on("click", ".toggle-password", function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $("#login_password");
    input.attr("type") === "password"
      ?
      input.attr("type", "text")
      :
      input.attr("type", "password");
  });
  $(document).on("click", ".toggle-password2", function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $("#confirm_password");
    input.attr("type") === "password"
      ?
      input.attr("type", "text")
      :
      input.attr("type", "password");
  });
  $(document).ready(function(){
      $("#confirm_password, #login_password").on("keyup", function(){
          var password = $("#login_password").val();
          var confirmPassword =$("#confirm_password").val();
          var message = $("#message");
          if (confirmPassword === "") {
              message.text("");
          } else if (password === confirmPassword) {
              message.text("Password match ✅").removeClass("text-danger").addClass("text-success");
          } else {
              message.text("Password do not match ❌").removeClass("text-success").addClass("text-danger");
          }
      });
  });
</script>