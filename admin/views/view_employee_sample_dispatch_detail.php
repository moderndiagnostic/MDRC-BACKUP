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
<!-- file upload  -->
<link href="lib/bootstrap-file/css/fileupload.css" rel="stylesheet" type="text/css" />
<!--image popup -->
<link href="lib/magnific-popup/css/magnific-popup.css" rel="stylesheet" type="text/css" />
<?php include('includes/menu.php'); ?>
<div class="content ht-100v pd-0">
  <?php include('includes/header.php'); ?>
  <div class="content-body">
    <div class="container">
      <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div class="d-sm-flex align-items-center justify-content-between">
          <h3 class="mg-b-0 tx-spacing--1"> Dispatch ID #<?= $this->sample_pickup['id']; ?></h3> <?= $this->badge; ?>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="index.php?view=employee_sample_dispatch_list"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> BACK</a>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6 col-md-6 col-12">
          <div data-label="Dispatch Detail" class="df-example demo-table">
            <?php
            $sample_pickup=$this->sample_pickup;
            $sample_pickupImage=$this->utility->get_image_url($sample_pickup["employee_sample_dispatch_other_detail_package_photo"],'sampleDispatch/'.$sample_pickup['id'],'large');
            $sample_receipt_photo=$this->utility->get_image_url($sample_pickup["employee_sample_dispatch_other_detail_receipt_photo"],'sampleDispatch/'.$sample_pickup['id'],'large');
            ?>
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="mb-2 tx-spacing--1"><?= $this->sample_pickup['courier_name']; ?></h4>
              <span class="alert alert-success pd-label-1 rounded-pill mb-0 btn-sm py-1 px-3"><?= $this->sample_pickup['courier_type']; ?></span>
            </div>
            <ul class="list-unstyled profile-info-list task-detail-list">
              <li><i data-feather="user"></i> <span class="tx-color-03"><?= $this->sample_pickup['courier_person']; ?></span></li>
              <li><i data-feather="phone"></i> <a href="tel:<?= $this->sample_pickup['courier_mobile']; ?>"><?= $this->sample_pickup['courier_mobile']; ?></a></li>
            </ul>
            <ul class="list-unstyled profile-info-list task-detail-list">
           
              <div class="row">
              <div class="col-lg-6 col-md-6 col-12">
              <span class="d-block"> Package Photo	 </span>
                <a href="<?=$sample_pickupImage;?>" class="image-popup">
                  <img src="<?=$sample_pickupImage;?>" class="wd-100">
                </a>
              </div>
              <div class="col-lg-6 col-md-6 col-12">
                <span class="d-block"> Receipt Photo  </span>
                <a href="<?=$sample_receipt_photo;?>" class="image-popup">
                  <img src="<?=$sample_receipt_photo;?>" class="wd-100">
                  </a>
              </div>
              </div>

             
            </ul>
            <?php if (count($this->sample_pickup) > 0) {
              $sample_pickup = $this->sample_pickup;
              $image = $this->utility->get_image_url($sample_pickup["employee_image"], 'employee', 'large');
              $image = $image != '' ? $image : SERVER_ROOT . '/uploads/default.png';
            ?>
              <hr class="my-4">
              <h5 class="mb-4 tx-spacing--1">Sent BY</h5>
              <div class="media mb-3">
                <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?= $image; ?>" class="rounded-circle w-100" alt=""></span>
                <div class="media-body">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <h5 class="mb-1 tx-spacing--1"><?= $sample_pickup['employee_name']; ?></h5>
                    <a href="tel:<?= $sample_pickup['mobile']; ?>" class="dr-call wd-30 ht-30 ms-3 d-flex align-items-center justify-content-center bg-primary rounded-pill"><span><i data-feather="phone" class="wd-15 ht-15 text-white"></i></span></a>
                  </div>
                  <p class="ml-0">Samples : <?= $sample_pickup['sample_count']; ?></p>
                  <p class="ml-0">Approx Delivery : <?= $sample_pickup['courier_delivery_date']; ?></p>
                </div>
              </div>
            <?php } ?>
            <?php if (count($this->sample_pickup) > 0) {
              $sample_pickup = $this->sample_pickup;
              $image = $this->utility->get_image_url($sample_pickup["image"], 'employee', 'large');
              $image = $image != '' ? $image : SERVER_ROOT . '/uploads/default.png';
            ?>
              <hr class="my-4">
              <h5 class="mb-4 tx-spacing--1">Sent To</h5>
              <div class="d-flex align-iteam-center">
                <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?= $image; ?>" class="rounded-circle w-100" alt=""></span>
                <div class="media-body">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <h5 class="mb-1 tx-spacing--1"><?= $sample_pickup['master_centre_name']; ?></h5>
                    <a href="tel:<?= $sample_pickup['master_centre_contact_mobile']; ?>" class="dr-call wd-30 ht-30 ms-3 d-flex align-items-center justify-content-center bg-primary rounded-pill"><span><i data-feather="phone" class="wd-15 ht-15 text-white"></i></span></a>
                  </div>
                  <p class="m-0"><?= $sample_pickup['master_centre_center_type']; ?></p>
                </div>
              </div>
            <?php } ?>
            <?php if (count($this->sample_pickup) > 0  && $sample_pickup['status'] == 'Delivered') {
              $sample_pickup = $this->sample_pickup;
              $image = $this->utility->get_image_url($sample_pickup["employee_s_image"], 'employee', 'large');
              $image = $image != '' ? $image : SERVER_ROOT . '/uploads/default.png';
            ?>
              <hr class="my-4">
              <h5 class="mb-4 tx-spacing--1">Receive BY</h5>
              <div class="d-flex align-iteam-center">
                <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?= $image; ?>" class="rounded-circle w-100" alt=""></span>
                <div class="media-body">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <h5 class="mb-1 tx-spacing--1"><?= $sample_pickup['employee_s_name']; ?></h5>
                    <a href="tel:<?= $sample_pickup['employee_s_mobile']; ?>" class="dr-call wd-30 ht-30 ms-3 d-flex align-items-center justify-content-center bg-primary rounded-pill"><span><i data-feather="phone" class="wd-15 ht-15 text-white"></i></span></a>
                  </div>
                  <p class="m-0"><?= $sample_pickup['receive_created_at']; ?></p>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
          <div data-label="Dispatch Summery" class="df-example demo-table mt-4">
            <div class="card p-0 shadow-none">
              <div class="card-body pd-0">
                <ul class="activity tx-13">
                  <?php
                    if($sample_pickup['status'] == 'Dispatched') {

                      $dispatche_status=$sample_pickup['status'];
                      $dispatche_time = $sample_pickup['created_at'];
                     
                    }
                    if ($sample_pickup['status'] == 'Delivered') {

                      $dispatche_status ="Dispatched";
                      $delivered_time = $sample_pickup['receive_created_at'];
                      $dispatche_time = $sample_pickup['created_at'];
                    }
                  ?>
                    <li class="activity-item pb-4">
                      <div class="activity-icon bg-primary-light tx-primary">
                        <i data-feather="clock"></i>
                      </div>
                      <div class="activity-body">
                        <h6><?=$dispatche_status;?></h6>
                        <p class="mg-b-2"><i data-feather="calendar" class="wd-15 ht-15 text-dark me-2"></i> <span><?= date('d-m-Y h:i A', strtotime($dispatche_time)); ?></span></p>
                      </div>
                    </li>
                   <?php if ($sample_pickup['status'] == 'Delivered') { ?>
                    <li class="activity-item pb-4">
                      <div class="activity-icon bg-primary-light tx-primary">
                        <i data-feather="clock"></i>
                      </div>
                      <div class="activity-body">
                        <h6><?= $sample_pickup['status']; ?></h6>
                        <p class="mg-b-2"><i data-feather="calendar" class="wd-15 ht-15 text-dark me-2"></i> <span><?= date('d-m-Y h:i A', strtotime($delivered_time)); ?></span></p>
                      </div>
                    </li>
                  <?php }; ?>
                </ul><!-- activity -->
              </div><!-- card-body -->
            </div><!-- card -->
          </div>
        </div>
        <!-- df-example -->
      </div>
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
<!-- other include -->
<script src="lib/alert/js/sweet-alert.min.js"></script>
<script src="lib/alert/js/jquery.sweet-alert.init.js"></script>
<script src="lib/validate/js/jquery.validate.min.js"></script>
<!-- image popup -->
<link href="lib/magnific-popup/css/magnific-popup.css" rel="stylesheet" type="text/css" />
<script src="lib/magnific-popup/js/jquery.magnific-popup.js"></script>
<!-- file upload  -->
<script src="lib/bootstrap-file/js/fileupload.js"></script>
<!-- Custom -->
<script src="scripts/js/grocery.js"></script>