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
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mg-b-10">
              <li class="breadcrumb-item"><a href="#">ABOUT VEENA</a></li>
              <li class="breadcrumb-item active" aria-current="page">
                <?=$this->manage_for?>
              </li>
            </ol>
          </nav>
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
          <div data-label="CSS" class="df-example demo-forms">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Head Start</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "head_start_css") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Head End</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "head_end_css") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Body Start</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "body_start_css") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Body End </label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "body_end_css") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="">
                  <label class="" for="example-email" style="font-style:italic"><strong>Note</strong> : Please write css with &lt;style&gt; &lt;/style&gt; tag.</label>
                </div>
              </div>
            </div>
          </div>
          
          <div data-label="HTML" class="df-example demo-forms">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Body Start</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "body_start_html") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Body End</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "body_end_html") ?>
                </div>
              </div>
            </div>
          </div>
          
          <div data-label="JS" class="df-example demo-forms">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Head Start</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "head_start_js") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Head End</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "head_end_js") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Body Start</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "body_start_js") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Body End</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "body_end_js") ?>
                </div>
              </div>
              
              
              
              
              
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="" for="example-email">Header Code</label>
                  <? $this->htmlBuilder->buildTag("textarea", array("class"=>"form-control"), "head_body") ?>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="">
                  <label class="" for="example-email" style="font-style:italic"><strong>Note</strong> : Please write code with &lt;script&gt; &lt;/script&gt; tag.</label>
                </div>
              </div>
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
<script>
      // Adding placeholder for search input
      (function($) {
        'use strict'
        var Defaults = $.fn.select2.amd.require('select2/defaults');
        $.extend(Defaults.defaults, {
          searchInputPlaceholder: ''
        });
        var SearchDropdown = $.fn.select2.amd.require('select2/dropdown/search');
        var _renderSearchDropdown = SearchDropdown.prototype.render;
        SearchDropdown.prototype.render = function(decorated) {
          // invoke parent method
          var $rendered = _renderSearchDropdown.apply(this, Array.prototype.slice.apply(arguments));
          this.$search.attr('placeholder', this.options.get('searchInputPlaceholder'));
          return $rendered;
        };
      })(window.jQuery);
      $(function(){
        'use strict'
        // Basic with search
        $('.select2').select2({
          placeholder: 'Choose one',
          searchInputPlaceholder: 'Search options'
        });
      });
    </script> 
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

