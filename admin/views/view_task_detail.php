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
          <h3 class="mg-b-0 tx-spacing--1 mr-2">Field Task ID #<?=$this->task['id'];?></h3>
          <?=$this->badge;?>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="index.php?view=task_list"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> BACK</a>
        </div>
      </div>

      <?= $this->utility->get_message() ?>
      <div class="row">
        <div class="col-lg-8 col-md-8 col-12">
          <div data-label="Client / Field Visit" class="df-example demo-table">
           <?php if($this->client['company_name']!=''){ 
            $client=$this->client;
            $clientCompanyName=$client['company_name'];
			      $clientAddress=$client['client_status']=='Client'?$client[0]['client_detail_area'].' '.$client[0]['city_name']:$client[0]['client_address_google_city'];
            $clientMobile=$client['mobile'];
            ?>
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="mb-2 tx-spacing--1"><?=$clientCompanyName;?></h4> 
              <span class="alert alert-success pd-label-1 rounded-pill mb-0 btn-sm py-1 px-3"><?=$client['client_status'];?></span>
            </div>
            <ul class="list-unstyled profile-info-list task-detail-list">
              <li><i data-feather="map-pin"></i> <span class="tx-color-03"><?=$clientAddress;?></span></li>
              <li><i data-feather="phone"></i> <a href="tel:<?=$clientMobile;?>"><?=$clientMobile;?></a></li>
            </ul>
            <?php } ?>
            <hr>
            <h4 class="mb-3 tx-spacing--1">Meeting Details</h4> 
            <div class="d-flex align-items-center">
              <label class="d-flex align-items-center mr-4"><i data-feather="calendar" class="wd-20 ht-20 mr-2"></i><?=date('d-m-Y h:i A', strtotime($this->task['created_at']));?></label>
            </div>
            <div class="remark mt-2">
              <label>Remark</label>
              <p class="m-0"><?=$this->task['task_remark'];?></p>
            </div>

            <hr class="my-4">
            <h4 class="mb-4 tx-spacing--1">Team</h4>
            <?php foreach($this->employee as $item) {
            $image=$this->utility->get_image_url($item["image"],'employee','large');
            $image=$image!=''?$image:SERVER_ROOT.'/uploads/default.png';
            ?>
            <div class="media mb-3">
              <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?=$image;?>" class="w-100"/></span>
              <div class="media-body">
                <div class="d-flex w-100 align-items-center justify-content-between">
                  <h5 class="mb-1 tx-spacing--1"><?=$item['name'];?></h5> 
                  <?php if($item['id']==$this->task['employee_primary_id']){ ?>
                  <label class="dr-active">Primary <span><i data-feather="check"></i></span></label>
                  <?php } ?>
                </div>
                <p class="m-0"><?=$item['master_designation_name'];?></p>
              </div>
            </div>
            <?php } ?>
            <?php if(!empty($this->finaUpdate)){ ?>
            <hr class="my-4">
            <h4 class="mb-4 tx-spacing--1">Task Summery</h4> 
            <div class="row summery">
              <div class="col-lg-4 col-md-4 col-12">
                <label class="tx-semibold">Do You meet Doctor?</label>
                <span class="d-flex align-items-center"><?=$this->finaUpdate['meetDr'];?></span>
              </div>
              <div class="col-lg-4 col-md-4 col-12">
                <label class="tx-semibold">Meeting Status</label>
                <span class="d-flex align-items-center"><?=$this->finaUpdate['meetingStatus'];?></span>
              </div>
            </div>
            <div class="remark mt-4">
              <label>Meeting Remark</label>
              <p class="m-0"><?=$this->finaUpdate['remark'];?></p>
            </div>
            <?php } ?>
          </div>

          <div data-label="Summery" class="df-example demo-table mt-4">
            <div class="card p-0 shadow-none">
              <div class="card-body pd-0">
                <ul class="activity tx-13">
                  <?php foreach($this->update as $item) {
                    $image='';
                    if($item['activity']=='Check In') {
                     $image=$this->utility->get_image_url($item["checkin_photo"],'taskUpdate','large');
                     $image=$image!=''?$image:'';
                    }
                  ?>
                  <li class="activity-item pb-4">
                    <div class="activity-icon bg-primary-light tx-primary">
                      <i data-feather="clock"></i>
                    </div>
                    <div class="activity-body d-flex justify-content-between">
                      <div>
                      <h6><?=$item['activity'];?></h6>
                      <p class="mg-b-2"><i data-feather="calendar" class="wd-15 ht-15 text-dark me-2"></i> <span><?=date('d-m-Y h:i A', strtotime($item['activity_time']));?></span></p>
                        <?php if($item['latitude']!='') {?>
                        <p class="text-dark mb-0"><a href="https://www.google.com/maps/search/?api=1&query=<?=$item['latitude'];?>,<?=$item['longitude'];?>" target="_blank"><i data-feather="map-pin" class="wd-15 ht-15 text-dark me-2"></i> <span>View Location</span></a></p>
                        <?php } ?>
                      </div>

                      <?php if($image!=''){ ?>
                      <div class="">
                        <a href="<?=$image;?>" class="image-popup">
                        <img src="<?=$image;?>" class="wd-100">
                        </a>
                      </div>
                      <?php } ?>

                     
                    </div>
                  </li>
                  <?php } ?>

                </ul><!-- activity -->
              </div><!-- card-body -->
            </div><!-- card -->
          </div>

        </div>

        <?php if(!empty($this->assigne)){ 
        $item=$this->assigne;
        ?>
        <div class="col-lg-4 col-md-4 col-12">
          <div class="deliverying assign-by df-example mt-0 px-3 py-2 rounded">
            <h6 class="bd-b py-2">Assign By</h6>
            <ul class="list-unstyled profile-info-list task-detail-list">
              <li><i data-feather="user"></i> <span class="tx-color-05 tx-semibold"><?=$item['name'];?></span></li>
              <li><i data-feather="briefcase"></i> <span class="tx-color-03"><?=$item['master_designation_name'];?></span></li>
              <li><i data-feather="phone"></i> <a href="tel:<?=$item['mobile'];?>"><?=$item['mobile'];?></a></li>
            </ul>
            <label class="mt-2 mb-2 tx-semibold">Assign Date & Time</label>
            <div class="d-flex align-items-center">
              <label class="d-flex align-items-center mr-4"><i data-feather="calendar" class="wd-20 ht-20 mr-2"></i><?=date('d-m-Y h:i A', strtotime($this->task['created_at']));?></label>
            </div>
          </div>
        </div>
        <?php } ?>
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
<script src="scripts/js/task.js<?=ALLVERSION ?>"></script>