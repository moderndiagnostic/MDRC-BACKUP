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
        <div>
          <h3 class="mg-b-0 tx-spacing--1"><?= $this->client['client_status']; ?> Detail</h3>
        </div>
        <div class="">
          <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="index.php?view=client_list"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> BACK</a>
        </div>
      </div>


      <div class="row">
        <div class="col-lg-3 col-md-3 col-12">
          <div class="profile-sidebar pd-lg-r-25">
            <div class="row">
              <div class="col-sm-3 col-md-2 col-lg-12">
                <div class="avatar avatar-lg"><img src="assets/img/profile.png" class="rounded-circle" alt=""></div>
              </div>

              <?php
              $client=$this->client;
              $clientImage=$this->utility->get_image_url($client["image"],'client','large');
              $clientCompanyName=$client['company_name'];
              $clientAddress=$client['client_status']=='Client'?$client[0]['client_detail_area'].' '.$client[0]['city_name']:$client[0]['client_address_google_city'];
              $clientMobile=$client['mobile'];
              ?>
              <div class="col-sm-8 col-md-7 col-lg-12 mg-t-20 mg-sm-t-0 mg-lg-t-25">
                <h5 class="mg-b-2 tx-spacing--1"><?=$clientCompanyName;?></h5>
                <p class="tx-color-03 mg-b-0"><?=$client['client_status'];?></p>
              </div>

              <div class="col-sm-6 col-md-5 col-lg-12 mg-t-20">
                <label class="tx-sans tx-10 tx-semibold tx-uppercase tx-color-01 tx-spacing-1 mg-b-15">Personal Information</label>
                <ul class="list-unstyled profile-info-list">
                  <li><i data-feather="phone"></i> <a href="tel:<?=$clientMobile;?>"><?=$clientMobile;?></a></li>
                  <li><i data-feather="mail"></i> <a href="mailto:<?=$client['email'];?>"><?=$client['email'];?></a></li>
                  <li><i data-feather="map-pin"></i> <span class="tx-color-03"><?=$client['city_name'];?></span></li>
                </ul>
              </div>

              <!-- <div class="col-sm-6 col-md-5 col-lg-12 mg-t-40">
                <label class="tx-sans tx-10 tx-semibold tx-uppercase tx-color-01 tx-spacing-1 mg-b-15">Reporting To</label>
                <ul class="list-unstyled profile-info-list">
                  <li><i data-feather="user"></i> <span class="tx-color-03">Allan Ray Palban</span></li>
                  <li><i data-feather="user"></i> <span class="tx-color-03">Senior Business Analyst</span></li>
                  <li><i data-feather="phone"></i> <a href="">(+1) 987 654 3201</a></li>
                </ul>
              </div> -->
              
            </div>
          </div>
        </div>
        <div class="col-lg-9 col-md-9 col-12 d-none">

          <div class="card card-profile-interest mg-b-20">
            <div class="card-header pd-y-15 pd-x-20 d-flex align-items-center justify-content-between">
              <h6 class="tx-uppercase tx-semibold mg-b-0">Employees</h6>
            </div>
            <div class="card-body pd-25">
              <div class="row">
                <div class="col-sm col-lg-12 col-xl">
                  <div class="media">
                    <div class="wd-45 ht-45 rounded d-flex align-items-center justify-content-center bg-white">
                    <span class="wd-50 ht-50 mr-3 d-flex align-items-center justify-content-center rounded-pill"><img src="https://www.mdrcindia.com/SalesApp/uploads/default.png" class="rounded-circle w-100" alt=""></span>
                    </div>
                    <div class="media-body pd-l-25">
                      <h6 class="tx-color-01 mg-b-5">Github, Inc.</h6>
                      <p class="tx-12 mg-b-10">Web-based hosting service for version control using Git... <a href="">Learn more</a></p>
                      <span class="tx-12 tx-color-03">6,182,220 Followers</span>
                    </div>
                  </div><!-- media -->

                  <div class="media">
                    <div class="wd-45 ht-45 bg-warning rounded d-flex align-items-center justify-content-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck tx-white-7 wd-20 ht-20">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                      </svg>
                    </div>
                    <div class="media-body pd-l-25">
                      <h6 class="tx-color-01 mg-b-5">DHL Express</h6>
                      <p class="tx-12 mg-b-10">Logistics company providing international courier service... <a href="">Learn more</a></p>
                      <span class="tx-12 tx-color-03">3,005,192 Followers</span>
                    </div>
                  </div><!-- media -->
                </div><!-- col -->
                <div class="col-sm col-lg-12 col-xl mg-t-25 mg-sm-t-0 mg-lg-t-25 mg-xl-t-0">
                  <div class="media">
                    <div class="wd-45 ht-45 bg-primary rounded d-flex align-items-center justify-content-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook tx-white-7 wd-20 ht-20">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                      </svg>
                    </div>
                    <div class="media-body pd-l-25">
                      <h6 class="tx-color-01 mg-b-5">Facebook, Inc.</h6>
                      <p class="tx-12 mg-b-10">Online social media and social networking service company... <a href="">Learn more</a></p>
                      <span class="tx-12 tx-color-03">12,182,220 Followers</span>
                    </div>
                  </div><!-- media -->

                  <div class="media">
                    <div class="wd-45 ht-45 bg-pink rounded d-flex align-items-center justify-content-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram tx-white-7 wd-20 ht-20">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                      </svg>
                    </div>
                    <div class="media-body pd-l-25">
                      <h6 class="tx-color-01 mg-b-5">Instagram</h6>
                      <p class="tx-12 mg-b-10">Photo and video-sharing social networking service by Facebook... <a href="">Learn more</a></p>
                      <span class="tx-12 tx-color-03">3,005,192 Followers</span>
                    </div>
                  </div><!-- media -->
                </div><!-- col -->
              </div><!-- row -->
            </div><!-- card-body -->
          </div>

          <div class="card pd-20 mg-b-20" style="display: none;">
            <div class="row row-sm">
              <div class="col-6 col-sm-4 col-md">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Market Cap</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">$14.5B</h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Volume (24h)</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">$4.6B</h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md mg-t-20 mg-sm-t-0">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">Change</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1 d-flex align-items-center">-$7.98 <small class="tx-danger position-relative t-1"><ion-icon name="arrow-down-outline" aria-label="arrow down outline" role="img" class="md hydrated"></ion-icon></small></h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md-3 col-xl mg-t-20 mg-md-t-0">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10"><span class="d-none d-sm-inline">Circulating </span>Supply</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">17.59M</h5>
              </div><!-- col -->
              <div class="col-6 col-sm-4 col-md mg-t-20 mg-md-t-0">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-03 mg-b-10">All Time High</h6>
                <h5 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1">$18.4K</h5>
              </div><!-- col -->
            </div><!-- row -->
          </div>

          <div class="card mg-b-20 mg-lg-b-25">
            <!-- card-header -->
            <div class="card-body pd-20 pd-lg-25">
              <ul class="nav nav-line" id="myTab5" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="profile-tab5" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Profile</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="clients-tab5" data-toggle="tab" href="#clients" role="tab" aria-controls="clients" aria-selected="false">Clients</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="teams-tab5" data-toggle="tab" href="#teams" role="tab" aria-controls="teams" aria-selected="false">Teams</a>
                </li>
              </ul>
              <!-- media -->
              <div class="tab-content mg-t-20" id="myTabContent5">
                <div class="tab-pane fade show active" id="profile" role="profile" aria-labelledby="profile-tab5">
                  <div class="col-lg-12 col-12 p-0">
                    <form method="post" name="profile_form" id="profile_form" data-parsley-validate="" novalidate="">
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Name <span class="tx-danger">*</span> </label>
                          <input name="name" id="name" type="text" class="form-control" value="" required="">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Mobile <span class="tx-danger">*</span> </label>
                          <input name="mobile" id="mobile" type="text" class="form-control" value="" required="">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Profile Photo <span class="tx-danger">*</span> </label>
                          <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new"> <img src="assets/img/profile.png" width="70" height="70px"> </div>
                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 100px; max-height: 100px; line-height: 20px;"> <img src="assets/img/profile.png"> </div>
                            <div>
                              <span class="btn btn-file btn-default p-0 my-2">
                                <span class="fileupload-new btn btn-white btn-xs">Select image</span>
                                <span class="fileupload-exists btn btn-white btn-xs"><i data-feather="edit" class="wd-10 mg-r-5"></i> Change</span>
                                <input name="image" id="image" type="file" class="">
                              </span>
                              <a href="#" class="btn btn-xs fileupload-exists btn-white" data-dismiss="fileupload"><i data-feather="trash" class="wd-10 mg-r-5"></i> Remove</a>
                            </div>
                            <div class="tx-12">(only Jpeg,jpg,png. Size Below 2 MB)</div>
                          </div>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Email <span class="tx-danger">*</span></label>
                          <input name="email" id="email" type="text" class="form-control" value="" required="">
                          <label for="inputEmail4" style="margin-top: 15px;">LMS employee Id <span class="tx-danger">*</span></label>
                          <input name="lms_employee_id" id="lms_employee_id" type="text" class="form-control" value="" required="">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Designation <span class="tx-danger">*</span> </label>
                          <select class="form-control select2">
                            <option value="0"> Select Designation</option>
                            <option value="1">Director</option>
                            <option value="2">Regional Sales Manager</option>
                            <option value="3">Zonal Sales Manager</option>
                            <option value="4">Business Development Executive</option>
                            <option value="5">General Manager</option>
                            <option value="6">Area Sales Manager</option>
                            <option value="7">Dupty General Manager</option>
                            <option value="8">MDRC Sales Admin</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">City <span class="tx-danger">*</span> </label>
                          <select class="form-control select2">
                            <option value="0"> Select City</option>
                            <option value="1">Gurgaon</option>
                            <option value="2">Panipat</option>
                            <option value="3">INDORE</option>
                            <option value="4">GUWAHATI</option>
                            <option value="5">Kolkata</option>
                            <option value="6">BHIWADI</option>
                            <option value="7">Jaipur</option>
                            <option value="8">BAREILLY</option>
                            <option value="9">Srinagar</option>
                            <option value="10">Gorakhpur-All</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Area</label>
                          <input name="area" id="area" type="text" class="form-control" value="" required="">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputEmail4">Status</label>
                          <select name="status" id="status" class="form-control" required="">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                          </select>
                        </div>
                        <div class="form-group col-md-12">
                          <label for="inputEmail4">Reporting To</label>
                          <select class="form-control select2">
                            <option value="0">Select Employee</option>
                            <option value="1">Dr.D.S.Yadav ( Director ) </option>
                            <option value="2">Dr.Nitin Kumar Yadav ( Director ) </option>
                            <option value="3">Mr.JITENDRA YADAV ( Director ) </option>
                          </select>
                        </div>
                        <div class="form-group col-md-12">
                          <button type="submit" class="btn btn-primary tx-13 submit-btn">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="tab-pane fade " id="clients" role="clients" aria-labelledby="clients-tab5">
                  <div class="table-responsive">
                    <table id="example1" class="table">
                      <thead>
                        <tr>
                          <th class="wd-20p">ID</th>
                          <th class="wd-20p">Name</th>
                          <th class="wd-25p">City</th>
                          <th class="wd-20p">Mobile</th>
                          <th class="wd-15p">Email</th>
                          <th class="wd-20p">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>Serge Baldwin</td>
                          <td>Gurgaon</td>
                          <td>1234567890</td>
                          <td>test@gmail.com</td>
                          <td>
                            <div class="d-flex">
                              <a title="Detail" href="#" class="btn btn-xs btn-warning btn-icon mg-r-5"><i class="fas fa-play"></i></a>
                              <a title="Edit" href="#" class="btn btn-xs btn-primary btn-icon mg-r-5"><i class="fas fa-edit"></i></a>
                              <button title="Delete" type="button" class="btn btn-xs btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="teams" role="teams" aria-labelledby="teams-tab5">
                  <div class="table-responsive">
                    <table id="example2" class="table">
                      <thead>
                        <tr>
                          <th class="wd-10p">ID</th>
                          <th class="wd-20p">Name</th>
                          <th class="wd-25p">Position</th>
                          <th class="wd-20p">Office</th>
                          <th class="wd-15p">Age</th>
                          <th class="wd-20p">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>Serge Baldwin</td>
                          <td>Data Coordinator</td>
                          <td>Singapore</td>
                          <td>64</td>
                          <td>
                            <div class="d-flex">
                              <a title="Detail" href="#" class="btn btn-xs btn-warning btn-icon mg-r-5"><i class="fas fa-play"></i></a>
                              <a title="Edit" href="#" class="btn btn-xs btn-primary btn-icon mg-r-5"><i class="fas fa-edit"></i></a>
                              <button title="Delete" type="button" class="btn btn-xs btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
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