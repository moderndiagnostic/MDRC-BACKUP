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
          <h3 class="mg-b-0 tx-spacing--1">Pickup ID #<?= $this->sample_pickup['id']; ?></h3> <?= $this->badge; ?>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="index.php?view=employee_sample_pickup_list"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> BACK</a>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6 col-md-6 col-12">
          <div data-label="Client Detail" class="df-example demo-table">
            <?php
            $client = $this->client;
            $clientImage = $this->utility->get_image_url($client["image"], 'client', 'large');
            $clientCompanyName = $client['company_name'];
            $clientAddress = $client['client_status'] == 'Client' ? $client[0]['client_detail_area'] . ' ' . $client[0]['city_name'] : $client[0]['client_address_google_city'];
            $clientMobile = $client['mobile'];
            ?>
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="mb-2 tx-spacing--1"><?= $clientCompanyName; ?></h4>
              <span class="alert alert-success pd-label-1 rounded-pill mb-0 btn-sm py-1 px-3"><?= $client['client_status']; ?></span>
            </div>
            <ul class="list-unstyled profile-info-list task-detail-list">
              <li><i data-feather="map-pin"></i> <span class="tx-color-03"><?= $clientAddress; ?></span></li>
              <li><i data-feather="phone"></i> <a href="tel:<?= $clientMobile; ?>"><?= $clientMobile; ?></a></li>
            </ul>
            <?php if (count($this->salesPerson) > 0) {
              $salesPerson = $this->salesPerson;
              $image = $this->utility->get_image_url($salesPerson["image"], 'employee', 'large');
              $image = $image != '' ? $image : SERVER_ROOT . '/uploads/default.png';
            ?>
              <hr class="my-4">
              <h5 class="mb-4 tx-spacing--1">Sales Person Tagged</h5>
              <div class="media mb-3">
                <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?= $image; ?>" class="rounded-circle w-100" alt=""></span>
                <div class="media-body">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <h5 class="mb-1 tx-spacing--1"><?= $salesPerson['name']; ?></h5>
                    <a href="tel:<?= $salesPerson['mobile']; ?>" class="dr-call wd-30 ht-30 ms-3 d-flex align-items-center justify-content-center bg-primary rounded-pill"><span><i data-feather="phone" class="wd-15 ht-15 text-white"></i></span></a>
                  </div>
                  <p class="m-0"><?= $salesPerson['master_designation_name']; ?></p>
                </div>
              </div>
            <?php } ?>
            <?php if (count($this->logistic) > 0) {
              $salesPerson = $this->logistic;
              $image = $this->utility->get_image_url($salesPerson["image"], 'employee', 'large');
              $image = $image != '' ? $image : SERVER_ROOT . '/uploads/default.png';
            ?>
              <hr class="my-4">
              <h5 class="mb-4 tx-spacing--1">Assign Logistic</h5>
              <div class="d-flex align-iteam-center">
                <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?= $image; ?>" class="rounded-circle w-100" alt=""></span>
                <div class="media-body">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <h5 class="mb-1 tx-spacing--1"><?= $salesPerson['name']; ?></h5>
                    <a href="tel:<?= $salesPerson['mobile']; ?>" class="dr-call wd-30 ht-30 ms-3 d-flex align-items-center justify-content-center bg-primary rounded-pill"><span><i data-feather="phone" class="wd-15 ht-15 text-white"></i></span></a>
                  </div>
                  <p class="m-0"><?= $salesPerson['master_designation_name']; ?></p>
                </div>
              </div>
            <?php } ?>
            <?php if (count($this->labDetail) > 0) {
              $labDetail = $this->labDetail;
              $image = $this->utility->get_image_url($labDetail["lab"], 'lab', 'large');
              $image = $image != '' ? $image : SERVER_ROOT . '/uploads/default.png';
            ?>
              <hr class="my-4">
              <h5 class="mb-4 tx-spacing--1">Processing Lab</h5>
              <div class="d-flex align-iteam-center">
                <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="<?= $image; ?>" class="rounded-circle w-100" alt=""></span>
                <div class="media-body">
                  <div class="d-flex w-100 align-items-center justify-content-between">
                    <h5 class="mb-1 tx-spacing--1"><?= $labDetail['name']; ?></h5>
                    <a href="tel:<?= $labDetail['mobile']; ?>" class="dr-call wd-30 ht-30 ms-3 d-flex align-items-center justify-content-center bg-primary rounded-pill"><span><i data-feather="phone" class="wd-15 ht-15 text-white"></i></span></a>
                  </div>
                  <p class="m-0"><?= $labDetail['address']; ?></p>
                </div>
              </div>
            <?php } ?>
          </div>
          <div data-label="Sample Collect List" class="df-example mt-4 demo-table">
            <div class="table-responsive">
              <table class="table table-bordered mg-b-0">
                <thead>
                  <tr>
                    <th class="wd-30p">Sr.</th>
                    <th class="wd-10p">Image</th>
                    <th class="wd-30p">Barcode</th>
                    <th class="wd-30p">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 0;
                  foreach ($this->pickup_images as $item) {
                    $image = $this->utility->get_image_url($item["image"], 'samplePickup/' . $this->sample_pickup['id'], 'large');
                    $i++;
                  ?>
                    <tr>
                      <td><?= $i; ?></td>
                      <td>
                        <a href="<?= $image; ?>" class="image-popup"><img src="<?= $image; ?>" class="wd-60 ht-60 rounded-3" alt=""></a>
                      </td>
                      <td><?= $item["barcode"]; ?></td>
                      <td><?= date('d-m-Y h:i A', strtotime($item['updated_at'])); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div><!-- table-responsive -->
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
              <div class="row">
                <div class="col-6 col-sm-4 col-md">
                  <div class="card pd-20 rounded-3">
                    <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Totle Samples</h6>
                    <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= count($this->pickup_images); ?></h5>
                  </div>
                </div>
                <div class="col-6 col-sm-4 col-md">
                  <div class="card pd-20 rounded-3">
                    <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Payment</h6>
                    <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">₹<?= $this->collectAmount; ?></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div data-label="Pic Summery" class="df-example demo-table mt-4">
            <div class="card p-0 shadow-none">
              <div class="card-body pd-0">
                <ul class="activity tx-13">
                  <?php foreach ($this->sample_pickup_update as $item) {
                    
                    $image='';
                    if($item['pickup_status']=='Check In') {
                     
                     $image=$this->utility->get_image_url($item["checkin_photo"],'samplePickupUpdate/' . $item['employee_id'],'large');
                     $image=$image!=''?$image:'';
                    }
                    $dis = '';
                    if ($item['pickup_status'] == 'Start Journey') {
                      $primaryLat = $item['latitude'];
                      $primaryLong = $item['longitude'];
                      $dis = '0 KM';
                    }
                    if ($item['pickup_status'] == 'End Journey') {
                      $dis = $this->utility->getDistance($primaryLat, $primaryLong, $item['latitude'], $item['longitude'], 'K');
                    }
                    if ($item['pickup_status'] == 'Check In') {
                      $dis = $this->utility->getDistance($primaryLat, $primaryLong, $item['latitude'], $item['longitude'], 'K');
                    }
                    if ($item['pickup_status'] == 'Check Out') {
                      $dis = $this->utility->getDistance($primaryLat, $primaryLong, $item['latitude'], $item['longitude'], 'K');
                    }
                  ?>
                    <li class="activity-item pb-4">
                      <div class="activity-icon bg-primary-light tx-primary">
                        <i data-feather="clock"></i>
                      </div>
                      <div class="activity-body">
                        <h6><?= $item['pickup_status']; ?></h6>
                        <p class="mg-b-2"><i data-feather="calendar" class="wd-15 ht-15 text-dark me-2"></i> <span><?= date('d-m-Y h:i A', strtotime($item['pickup_date'])); ?></span></p>
                        <?php if ($item['latitude'] != '') { ?>
                          <p class="text-dark mb-0">
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= $item['latitude']; ?>,<?= $item['longitude']; ?>" target="_blank"><i data-feather="map-pin" class="wd-15 ht-15 text-dark me-2"></i> <span>View Location</span></a>
                            <?= $dis; ?>
                          </p>
                        <?php } ?>
                      </div>
                      <?php if($image!=''){ ?>
                          <div class="">
                            <a href="<?=$image;?>" class="image-popup">
                            <img src="<?=$image;?>" class="wd-100">
                            </a>
                          </div>
                        <?php } ?>
                    </li>
                  <?php } ?>
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