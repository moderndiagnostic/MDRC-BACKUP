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
<!--<link rel="stylesheet" href="assets/css/skin.charcoal.css">-->

<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/custom.css">

<!-- file upload  -->
<link href="lib/bootstrap-file/css/fileupload.css" rel="stylesheet" type="text/css" />

<!--image popup -->
<link href="lib/magnific-popup/css/magnific-popup.css" rel="stylesheet" type="text/css" />

<!--Sweet Alert CSS & JS -->
<link href="lib/alert/css/sweet-alert.css" rel="stylesheet" type="text/css" />

<?php include('includes/menu.php'); ?>
<div class="content ht-100v pd-0">
  <?php include('includes/header.php'); ?>
  <!-- content-header -->
  <div class="content-body">
    <div class="container pd-x-0">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mg-b-10">
              <li class="breadcrumb-item"><a href="#">Client</a></li>
              <li class="breadcrumb-item active" aria-current="page">
              <?= $this->manage_for;?> <?=$this->to_do ?>
              </li>
            </ol>
          </nav>
          <h4 class="mg-b-0 tx-spacing--1">
            <?= $this->manage_for;?> <?=$this->to_do ?>
          </h4>
        </div>
        <div class="d-none d-md-block"> </div>
      </div>
      <?= $this->utility->get_message() ?>
      <? $this->htmlBuilder->buildTag("form", array("action" => "", "data-parsley-validate" => "", "class" => "form-horizontal form-bordered form-validate"), "client_form"); ?>
      <? $this->htmlBuilder->buildTag("input", array("type" => "hidden", "value" => $this->getGetVar('id')), "id"); ?>
      <? $this->htmlBuilder->buildTag("input", array("type" => "hidden", "value" => "update_data"), "act"); ?>
      <div class="row">
        <div class="col-lg-12">

          <div data-label="Project Location" class="df-example demo-forms projectsAll OtherDivs">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail4">Company Name <span class="tx-danger">*</span></label>
                <? $this->htmlBuilder->buildTag("input", array("class" => "form-control ", "type" => "text", "required" => ""), "company_name"); ?>
              </div>

              <div class="form-group col-md-6">
                <label for="inputEmail4">Phone </label>
                <? $this->htmlBuilder->buildTag("input", array("class" => "form-control", "type" => "text"), "phone"); ?>
              </div>

              <div class="form-group col-md-6">
                <label for="inputEmail4">Email <span class="tx-danger">*</span></label>
                <? $this->htmlBuilder->buildTag("input", array("class" => "form-control ", "type" => "text", "required" => ""), "email"); ?>
              </div>

              <div class="form-group col-md-6">
                <label for="inputEmail4">Mobile <span class="tx-danger">*</span> </label>
                <? $this->htmlBuilder->buildTag("input", array("class" => "form-control", "type" => "text"), "mobile"); ?>
              </div>

              <div class="form-group col-md-12">
                <label for="inputEmail4">Description</label>
                <? $this->htmlBuilder->buildTag("textarea", array("class" => "form-control ckeditor"), "short_desc"); ?>
              </div>

              <div class="form-group col-md-6">
                <?php
                $folder = 'client';
                $image = $this->rscat["image"];
                if ($this->rscat['image'] != '' && file_exists(ABS_PATH . "/uploads/client/" . $this->rscat['image'])) {
                  $client_image_file = '<a href="../uploads/client/' . $this->rscat['image'] . '" target="_blank">' . $this->rscat['image'] . '</a>';
                }
                ?>
                <label for="inputEmail4">Image</label>
                <? $this->htmlBuilder->buildTag("input", array("class" => "form-control ", "type" => "file"), "image"); ?>
                <?= $client_image_file ?>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <button class="btn btn-primary client_modal_submit" type="submit">Submit</button>
          <a class="btn btn-secondary" href="index.php?view=client_list">Cancel</a>
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
<script src="lib/validate/js/jquery.validate.min.js"></script>

<!-- file upload  -->
<script src="lib/bootstrap-file/js/fileupload.js"></script>

<!-- image popup -->
<script src="lib/magnific-popup/js/jquery.magnific-popup.js"></script>

<!-- Custom -->
<script src="scripts/js/grocery.js"></script>
<script src="scripts/js/client.js"></script>

<script src="lib/jqueryui/jquery-ui.min.js"></script>
<script src="lib/editor/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
  $(".select2").select2();
</script>