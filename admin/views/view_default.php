<!-- vendor css -->
<link href="lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
<!-- DashForge CSS -->
<link rel="stylesheet" href="assets/css/dashforge.css">
<link rel="stylesheet" href="assets/css/dashforge.auth.css">
<!-- Skin CSS -->
<link rel="stylesheet" href="assets/css/skin.cool.css">
<!--<link rel="stylesheet" href="assets/css/skin.charcoal.css">-->
<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/custom.css">
<!--Sweet Alert CSS & JS -->
<link href="lib/alert/css/sweet-alert.css" rel="stylesheet" type="text/css" />

<!-- navbar -->
<div class="content content-fixed content-auth">
  <div class="container">
    <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
      <!-- <div class="media-body align-items-center d-none d-lg-flex">
        <div class="mx-wd-600"> <img src="assets/img/login-page.png" class="img-fluid" alt=""> </div>
      </div> -->
      <!-- media-body -->
      <div class="sign-wrapper">
        <form method="post" name="adminLoginForm" id="adminLoginForm" data-parsley-validate>
          <div class="wd-100p">
            <h3 class="tx-color-01 mg-b-5">Sign In</h3>
            <p class="tx-color-03 tx-14 mg-b-20">WELCOME BACK! PLEASE SIGNIN TO CONTINUE.</p>
            <div class="form-group">
              <label>Email / Mobile / Employee ID</label>
              <input type="text" name="loginEmail" id="loginEmail" class="form-control" required="required" placeholder="Enter your Email / Mobile / Employee ID" autocomplete="off">
            </div>


            <div class="form-group">
              <div class="d-flex justify-content-between mg-b-5">
                <label class="mg-b-0-f">Password</label>
                <a href="#modal2" data-toggle="modal" class="tx-13">Forgot password?</a>
              </div>
              <div class="input-group">
                <input type="password" name="loginPass" id="loginPass" class="form-control" required placeholder="Enter your password / App Password" autocomplete="off">
                <div class="input-group-prepend">
                  <div class="input-group-text"> <i toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"></i> </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-brand-02 btn-block adminLoginSubmit">Sign In</button>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="customCheck3" name="loginRemember" value="Yes" checked>
              <label class="custom-control-label" for="customCheck3">Keep Me Logged In For 1 Week</label>
            </div>

          </div>
        </form>
      </div>
      <!-- sign-wrapper -->
    </div>
    <!-- media -->
  </div>
  <!-- container -->
</div>
<!-- content -->
<!-- All modal -->
<div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Forgot Password</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="adminForgotForm" id="adminForgotForm" data-parsley-validate>
        <div class="modal-body">
          <p>Enter your email address to recover your store admin password.</p>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail4">Enter Email</label>
              <input type="email" class="form-control" name="forgotEmail" id="forgotEmail" placeholder="Email" autocomplete="off" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 adminForgotSubmit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- end all modal -->
<footer class="footer">
  <div> <span>&copy; 2023 <?=PROJECT_TILLE ?></span> </div>
</footer>
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="lib/feather-icons/feather.min.js"></script>
<script src="lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="lib/prismjs/prism.js"></script>
<script src="lib/parsleyjs/parsley.min.js"></script>
<script src="assets/js/dashforge.js"></script>
<!-- other include -->
<script src="lib/alert/js/sweet-alert.min.js"></script>
<script src="lib/alert/js/jquery.sweet-alert.init.js"></script>
<script src="lib/validate/js/jquery.validate.min.js"></script>
<!-- Custom -->
<script src="scripts/js/grocery.js"></script>
<script src="scripts/js/admin.js"></script>
<script>
  $(document).on("click", ".toggle-password", function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $("#loginPass");
    input.attr("type") === "password"? input.attr("type", "text"):input.attr("type", "password");
  });
</script>