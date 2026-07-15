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
          <h3 class="mg-b-0 tx-spacing--1 mr-2">Logistic Confirmation #<?=$this->journey_data['id'];?></h3>
          <?=$this->statusBadge;?>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="index.php?view=journey_list"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> BACK</a>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
          <div data-label="Journey Detail" class="df-example demo-table">
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="mb-2 tx-spacing--1"><?=$this->journey_data['employee_name'];?></h4>
              <?=$this->emp_status?>
            </div>
            <ul class="list-unstyled profile-info-list journey_data-detail-list">
              <li><i data-feather="mail"></i> <a href="mailto:<?=$this->journey_data['employee_email'];?>" class="tx-color-03"><?=$this->journey_data['employee_email'];?></a></li>
              <?php if(!empty($this->journey_data['employee_mobile'])){ ?><li><i data-feather="phone"></i> <a href="tel:<?=$this->journey_data['employee_mobile'];?>"><?=$this->journey_data['employee_mobile'];?></a></li><?php } ?>
            </ul>
            <hr>
            <h4 class="mb-3 tx-spacing--1">Journey Details</h4>
            <div class="row">
              <div class="remark col-md-3 mb-2">
                <label class="mb-0">Journey Date</label>
                <p class="m-0"><?=date('d-m-Y', strtotime($this->journey_data['journey_date']));?></p>
              </div>
              <div class="remark col-md-3 mb-2">
                <label class="mb-0">Start KM</label>
                <p class="m-0"><?=$this->journey_data['start_km'];?></p>
              </div>
              <div class="remark col-md-3 mb-2">
                <label class="mb-0">End KM</label>
                <p class="m-0"><?=$this->journey_data['end_km'];?></p>
              </div>
              <div class="remark col-md-3 mb-2">
                <label class="mb-0">Total KM</label>
                <p class="m-0"><?=$this->journey_data['total_km'];?></p>
              </div>

              <!-- REJECT BY MANAGER -->
              <?php if(!empty($this->journey_data['employee_daily_journey_detail_manager_remark']) && !empty($this->journey_data['employee_daily_journey_detail_manager_datetime'])) { ?>
                <div class="remark col-md-6 mb-2">
                  <label class="mb-0">Reject By Manager</label>
                  <p class="m-0"><?=$this->manager_name?></p>
                  <p class="m-0"><?=date('d-m-Y h:i A',strtotime($this->journey_data['employee_daily_journey_detail_manager_datetime']));?></p>
                </div>
                <div class="remark col-md-6 mb-2">
                  <label class="mb-0">Manager Remark</label>
                  <p class="m-0"><?=$this->journey_data['employee_daily_journey_detail_manager_remark'];?></p>
                </div>
              <!-- APPROVED BY MANAGER -->
              <?php } if(empty($this->journey_data['employee_daily_journey_detail_manager_remark']) && !empty($this->journey_data['employee_daily_journey_detail_manager_datetime'])) { ?>
                <div class="remark col-md-6 mb-2">
                  <label class="mb-0">Approved By Manager</label>
                  <p class="m-0"><?=$this->manager_name?></p>
                  <p class="m-0"><?=date('d-m-Y h:i A',strtotime($this->journey_data['employee_daily_journey_detail_manager_datetime']));?></p>
                </div>
              <!-- REJECT BY FINANCE -->
              <?php } if(!empty($this->journey_data['employee_daily_journey_detail_finance_remark']) && !empty($this->journey_data['employee_daily_journey_detail_finance_datetime'])) { ?>
                <div class="remark col-md-6 mb-2">
                  <label class="mb-0">Reject By Finance</label>
                  <p class="m-0"><?=$this->finance_name?></p>
                  <p class="m-0"><?=date('d-m-Y h:i A',strtotime($this->journey_data['employee_daily_journey_detail_finance_datetime']));?></p>
                </div>
                <div class="remark col-md-6 mb-2">
                  <label class="mb-0">Finance Remark</label>
                  <p class="m-0"><?=$this->journey_data['employee_daily_journey_detail_finance_remark'];?></p>
                </div>
              <!-- APPROVED BY FINANCE -->
              <?php } if(!empty($this->journey_data['employee_daily_journey_detail_finance_datetime']) && empty($this->journey_data['employee_daily_journey_detail_finance_remark'])) { ?>
                <div class="remark col-md-6 mb-2">
                  <label class="mb-0">Approved By Finance</label>
                  <p class="m-0"><?=$this->finance_name?></p>
                  <p class="m-0"><?=date('d-m-Y h:i A',strtotime($this->journey_data['employee_daily_journey_detail_finance_datetime']));?></p>
                </div>
              <?php } ?>
            </div>
          </div>

          <div data-label="Summery" class="df-example demo-table mt-4">
            <div class="card p-0 shadow-none">
              <div class="card-body pd-0">
                <?php foreach($this->journey_detail as $item) {
                  if($item['title']=='Start Journey'){
                    $image = '../uploads/daily_journey/'.$item['employee_daily_journey_start_image'];
                    $latitude = $item['employee_daily_journey_start_latitude'];
                    $longitude = $item['employee_daily_journey_start_longitude'];
                  } elseif($item['title']=='End Journey'){
                    $image = '../uploads/daily_journey/'.$item['employee_daily_journey_end_image'];
                    $latitude = $item['employee_daily_journey_end_latitude'];
                    $longitude = $item['employee_daily_journey_end_longitude'];
                  } else {
                    $image = '';
                    $latitude = '';
                    $longitude = '';
                  }
                  ?>
                  <ul class="activity tx-13">
                    <li class="activity-item pb-4">
                      <div class="activity-icon bg-primary-light tx-primary">
                        <i data-feather="clock"></i>
                      </div>
                      <div class="activity-body d-flex justify-content-between">
                        <div>
                          <h6><?=$item['title']?></h6>
                          <p class="mg-b-2"><i data-feather="calendar" class="wd-15 ht-15 text-dark me-2"></i> <span><?=date('d-m-Y h:i A',strtotime($item['created_at']))?></span></p>
                          <?php if(!empty($latitude) && !empty($longitude)) { ?>
                            <p class="text-dark mb-0"><a href="https://www.google.com/maps/search/?api=1&query=<?=$latitude?>,<?=$longitude?>" target="_blank"><i data-feather="map-pin" class="wd-15 ht-15 text-dark me-2"></i> <span>View Location</span></a></p>
                          <?php } ?>
                        </div>
                        <?php if(!empty($image)) { ?>
                        <div class="">
                          <a href="<?=$image?>" class="image-popup">
                            <img src="<?=$image?>" class="wd-100">
                          </a>
                        </div>
                        <?php } ?>
                      </div>
                    </li>
                  </ul>
                <?php } ?>
              </div>
            </div>
          </div>

          <div data-label="Sample Pickup Details" class="df-example demo-table mt-4">
            <div class="card p-0 shadow-none">
              <div class="card-body pd-0">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped mb-0">
                    <thead class="table-primary">
                      <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Distance</th>
                        <th>Created Date</th>
                        <th>Summary</th>
                        <th>Status</th>
                        <th>Image</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $totalKm = 0;
                      if(count($this->data_sample)>0) { 
                        foreach($this->data_sample as $sample){ 
                          $distance = floatval(str_replace(" km", "", $sample['distance_km']));
        			            $totalKm += $distance;
                          $summary='Collect Sample : <b>'.$sample['collect_sample'].'</b>';
		                      $summary.='<br/>Collect Payment : <b>'.$sample['collect_payment'].'</b>';

                          if($sample['status'] == 'Pending'){
                            $status = '<span class="badge badge-warning">Pending</span>';
                          } elseif($sample['status'] == 'In Progress'){
                            $status = '<span class="badge badge-primary">In Progress</span>';
                          } elseif($sample['status'] == 'Completed'){
                            $status = '<span class="badge badge-success">Completed</span>';
                          }
                          $banner_img='';
                          $obj_table = $this->load_model("employee_sample_pickup_update");
                          $checkIN = $obj_table->execute("SELECT", false, "", "pickup_status='Check In' and employee_sample_pickup_id='".$sample["id"]."'");
                          if(count($checkIN)>0){
                            $banner_img=$this->utility->get_image_path($checkIN[0]['checkin_photo'],'samplePickupUpdate/'.$checkIN[0]['employee_id'],"large");
                          }
                          ?>
                      <tr>
                        <td><?=$sample['id']?></td>
                        <td><?=$sample['client_company_name']?></td>
                        <td><?=$sample['distance_km']?></td>
                        <td><?=date('d-m-Y h:i A', strtotime($sample['created_at']));?></td>
                        <td><?=$summary?></td>
                        <td><?=$status?></td>
                        <td><a href="<?=$banner_img?>" class="image-popup"><img src="<?=$banner_img?>" class="wd-100" /></a></td>
                      </tr>
                      <?php } } else { ?>
                      <tr>
                        <td colspan="7" class="text-center">No sample pickups found.</td>
                      </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="4" class="text-end">Total KM:</th>
                        <th colspan="3"><?=$totalKm?> km</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>


        </div>
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