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
        <div class="">
          <h3 class="mg-b-0 tx-spacing--1">Employee Detail</h3>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="index.php?view=employee_list"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> BACK</a>
        </div>
      </div>
      <?= $this->utility->get_message() ?>
      <div class="row">
        <div class="col-lg-3 col-md-3 col-12">
          <div class="profile-sidebar pd-lg-r-25">
            <div class="row">
              <div class="col-sm-3 col-md-2 col-lg-12">
                <?php
                $employee=$this->employee;
                $image=$this->utility->get_image_url($employee["image"],'employee','large');
                $image=$image!=''?$image:SERVER_ROOT.'/uploads/default.png';
                ?>
                <div class="avatar avatar-lg"><img src="<?=$image;?>" class="rounded-circle" alt=""></div>
              </div>
              <div class="col-sm-8 col-md-7 col-lg-12 mg-t-20 mg-sm-t-0 mg-lg-t-25">
                <h5 class="mg-b-2 tx-spacing--1"><?=$this->employee['name'];?></h5>
                <p class="tx-color-03 mg-b-0"><?=$this->employee['master_designation_name'];?></p>
                <p class="tx-color-03 mg-b-0"><?=$this->employee['lms_employee_code'];?></p>
                <input type="hidden" name="lms_employee_id" id="lms_employee_id" value="<?=$this->employee['lms_employee_id'];?>"/>
              </div>
              <div class="col-sm-6 col-md-5 col-lg-12 mg-t-20">
                <label class="tx-sans tx-10 tx-semibold tx-uppercase tx-color-01 tx-spacing-1 mg-b-15">Personal Information</label>
                <ul class="list-unstyled profile-info-list">
                  <li><i data-feather="phone"></i> <a href="tel:<?=$this->employee['mobile'];?>"><?=$this->employee['mobile'];?></a></li>
                  <li><i data-feather="mail"></i> <a href="mailto:<?=$this->employee['email'];?>"><?=$this->employee['email'];?></a></li>
                  <li><i data-feather="map-pin"></i> <span class="tx-color-03"><?=$this->employee['city_name'];?></span></li>
                </ul>
              </div>

              <?php if(!empty($this->reportingDetail)){ ?>
              <div class="col-sm-6 col-md-5 col-lg-12 mg-t-40">
                <label class="tx-sans tx-10 tx-semibold tx-uppercase tx-color-01 tx-spacing-1 mg-b-15">Reporting To</label>
                <ul class="list-unstyled profile-info-list">
                  <li><i data-feather="user"></i> <span class=""><?=$this->reportingDetail['name'];?></span></li>
                  <li><i data-feather="user"></i> <span class="tx-color-03"><?=$this->reportingDetail['master_designation_name'];?></span></li>
                  <li><i data-feather="user"></i> <span class="tx-color-03"><?=$this->reportingDetail['lms_employee_code'];?></span></li>
                  <li><i data-feather="phone"></i> <a href="tel:<?=$this->reportingDetail['mobile'];?>"><?=$this->reportingDetail['mobile'];?></a></li>
                </ul>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="col-lg-9 col-md-9 col-12">
          <div class="card pd-20 mg-b-20 mg-lg-b-25 d-none">
            <div class="row row-sm">
              <div class="col-6 col-sm-4 col-md">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Task</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">100</h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Team</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">0</h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md mg-t-20 mg-sm-t-0">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Client</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1 d-flex align-items-center">0</h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md-3 col-xl mg-t-20 mg-md-t-0">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">last Login</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"></h5>
              </div><!-- col -->
              
            </div><!-- row -->
          </div>
          <div class="card mg-b-20 mg-lg-b-25">
            <!-- card-header -->
            <div class="card-body pd-20 pd-lg-25">
              <ul class="nav nav-line" id="myTab5" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="team-tab5" data-toggle="tab" href="#team" role="tab" aria-controls="team" aria-selected="false">Team</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="client-tab5" data-toggle="tab" href="#client" role="tab" aria-controls="client" aria-selected="false">Client</a>
                </li>
              </ul>
              <!-- media -->
              <div class="tab-content mg-t-20" id="myTabContent5">
                <div class="tab-pane fade show active" id="team" role="team" aria-labelledby="team-tab5">
                  <div class="table-responsive">
                    <table id="table_employee_team" class="table">
                      <thead>
                        <tr>
                          <th class="wd-10p">ID</th>
                          <th class="wd-40p">Name</th>
                          <th class="wd-30p">Contact</th>
                          <th class="wd-20p">Action</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="client" role="client" aria-labelledby="client-tab5">
                  <div class="table-responsive">
                    <table id="table_employee_client" class="table">
                      <thead>
                        <tr>
                          <th class="wd-10p">ID</th>
                          <th class="wd-40p">Company Name</th>
                          <th class="wd-25p">Contact</th>
                          <th class="wd-10p">Status</th>
                          <th class="wd-15p">Action</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- card-footer -->
          </div>
        </div>
      </div>
      <!-- df-example -->
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
<script src="scripts/js/employee_detail.js<?= ALLVERSION ?>"></script>