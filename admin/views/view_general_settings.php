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
<?php include('includes/menu.php');?>
<div class="content ht-100v pd-0">
  <?php include('includes/header.php');?>
  <!-- content-header -->
  <div class="content-body">
    <div class="container pd-x-0">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
          <h4 class="mg-b-0 tx-spacing--1">
            <?=$this->to_do?>
            <?=$this->manage_for?>
          </h4>
        </div>
        <div class="d-none d-md-block"> </div>
      </div>
      <?=$this->utility->get_message()?>
      <? $this->htmlBuilder->buildTag("form", array("action"=>"","data-parsley-validate"=>"","class"=>"form-horizontal form-bordered form-validate"), "frm_generel_settings");?>
      <? $this->htmlBuilder->buildTag("input", array("type"=>"hidden", "value"=>$this->id), "id");?>
      <? $this->htmlBuilder->buildTag("input", array("type"=>"hidden", "value"=>"update_data"), "act");?>
      <div class="row">
        <div class="col-lg-12">
          <?php 
          if($this->rs_data['logo_file']!='' && file_exists(ABS_PATH."/uploads/logo/".$this->rs_data['logo_file']))
          {
            $logo='<a href="../uploads/logo/'.$this->rs_data['logo_file'].'" target="_blank">'.$this->rs_data['logo_file'].'</a>';
          }
          ?>

          <div data-label="Contact Details" class="df-example demo-forms d-none">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="control-label" for="example-email">Primary Logo</label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"file","class"=>"form-control"), "logo_file") ?>
                  <?=$logo?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="" for="example-email">Image Alt</label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control"), "image_alt") ?>
                </div>
              </div>
    
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Address</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("type"=>"text","class"=>"form-control","value"=>$this->rs_data['address']), "address") ?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="control-label" for="example-email">Contact number </label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control numbersOnly number"), "contact_number") ?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="control-label" for="example-email">Whatsapp number </label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control numbersOnly number"), "contact_number1") ?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="control-label" for="example-email">Email </label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control"), "contact_email") ?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="control-label" for="example-email">Email 2 </label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control"), "contact_email1") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Copyright</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control ckeditor"), "footer_text") ?>
                </div>
              </div>
            </div>
          </div>
          
          
          <div data-label="Receive Enquiry Emails" class="df-example demo-forms d-none">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Contact Email (To)</label>
                  <? $this->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","placeholder"=>"xyz@gmail.com"), "to_emails") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="control-label" for="example-email">Contact Emails (CC) </label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control ","placeholder"=>"xyz@gmail.com,abc@gmail.com"), "cc_emails") ?>
                </div>
              </div>
            </div>
          </div>

          <div data-label="All Settings" class="df-example demo-forms">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Select Finance Manager</label>
                  <? $this->htmlBuilder->buildTag("select", array("type"=>"text","class"=>"form-control select2" ,"values"=>$this->employee), "finance_manager_id") ?>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
      <div class="row mg-t-15">
        <div class="col-lg-12">
          <button class="btn btn-primary" type="submit">Submit</button>
          <button class="btn btn-secondary" type="reset">Cancel</button>
        </div>
      </div>
      </form>
      <?php include('includes/footer.php');?>
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

