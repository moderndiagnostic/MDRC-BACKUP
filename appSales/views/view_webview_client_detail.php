<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
<main class=" mb-xxl">
    <section>
        <div class="card border-0 rounded-0 mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <?php
                        if(!empty($this->client["image"])){
                            $image=$this->utility->get_image_url($this->client["image"],'client','large');
                        }else{
                            $image=SERVER_ROOT."/uploads/user.png";;
                        }
                        ?>
                        <img src="<?=$image?>" alt="" class="avtar-md">
                    </div>
                    <div class="ms-2">
                        <h6 class="text-main mb-0 line-clamp-1"><?=$this->client['company_name']?></h6>
                        <div class="d-flex align-items-start mt-1">
                            <a class="mb-0 content-color ms-1 text-decoration-none" href="tel:<?=$this->client['mobile']?>"><i
                                    class="fa-solid fa-phone text-main me-1"></i><?=$this->client['mobile']?></a>
                        </div>
                    </div>
                </div>
                <div class="address-box mt-2">
                    <h6 class="text-main mb-0 fs-6">Address</h6>
                    <p class="mb-0 content-color"><?=$this->client['client_status']=='Client'?$this->client['client_detail_area'].' '.$this->client['city_name']:$this->client['client_address_google_city']?></p>
                </div>
            </div>
        </div>
        <?php if(count($this->seals_person)>0) { ?>
        <div class="card border-0 rounded-0 mb-3">
            <div class="card-body">
                <h6>Sales Person Assigned</h6>
                <div class="d-flex align-items-center">
                    <div>
                        <?php
                            if(!empty($this->seals_person["image"])){
                                $image=$this->utility->get_image_url($this->seals_person["image"],'employee','large');
                            }else{
                                $image=SERVER_ROOT."/uploads/user.png";;
                            }
                        ?>
                        <img src="<?=$image?>" alt="" class="avtar-sm">
                    </div>
                    <div class="ms-2">
                        <h6 class="mb-0 line-clamp-1 tx-16"><?=$this->seals_person['name']?></h6>
                        <p class="mb-0 content-color line-clamp-1"><?=$this->seals_person['lms_employee_code']?></p>
                    </div>
                    <div class="ms-auto call-box">
                        <a href="tel:<?=$this->seals_person['mobile']?>">
                            <i class="fa-solid fa-phone"></i>
                        </a>
                    </div>
                </div>
                <div class="summery-count mt-2">
                    <h6 class="tx-14">This Month Summary</h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-main mb-0 fw-bold"><?=$this->task['meetCount']?></h5>
                                    <p class="mb-0 tx-14">Total Client Meet</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-main mb-0 fw-bold"><?=$this->task['count']?></h5>
                                    <p class="mb-0 tx-14">Total Visit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer rounded-0 border-top bg-white text-center">
                <a href="index.php?view=webview_blank&redirect=sales&clientId=<?=$this->utility->encrypt($this->client["id"])?>&selectEmployeeId=<?=$this->utility->encrypt($this->seals_person["id"])?>&employeeId=<?=$this->utility->encrypt($this->seals_person["id"])?>" class="text-main text-decoration-none">View All Sales Visits <i class="fa-solid fa-chevron-right ms-1"></i></a>
            </div>
        </div>
        <?php } else {?>
        <div class="card border-0 rounded-0 mb-3">
            <div class="card-body">
                <h6>Sales Person Assigned</h6>
                <div class="d-flex align-items-center">
                    <div>
                        <?php
                            $image=SERVER_ROOT."/uploads/user.png";;
                        ?>
                        <img src="<?=$image?>" alt="" class="avtar-sm">
                    </div>
                    <div class="ms-2">
                        <h6 class="mb-0 line-clamp-1 tx-16">NA</h6>
                        <p class="mb-0 content-color line-clamp-1">NA</p>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if(count($this->logistic_person)>0) { ?>
        <div class="card border-0 rounded-0">
            <div class="card-body">
                <h6>Logistics Person Assigned</h6>
                <div class="d-flex align-items-center">
                    <div>
                        <?php
                            if(!empty($this->logistic_person["image"])){
                                $image=$this->utility->get_image_url($this->logistic_person["image"],'employee','large');
                            }else{
                                $image=SERVER_ROOT."/uploads/user.png";;
                            }
                        ?>
                        <img src="<?=$image?>" alt="" class="avtar-sm">
                    </div>
                    <div class="ms-2">
                        <h6 class="mb-0 line-clamp-1 tx-16"><?=$this->logistic_person['name']?></h6>
                        <p class="mb-0 content-color line-clamp-1"><?=$this->logistic_person['lms_employee_code']?></p>
                    </div>
                    <div class="ms-auto call-box">
                        <a href="tel:<?=$this->logistic_person['mobile']?>">
                            <i class="fa-solid fa-phone"></i>
                        </a>
                    </div>
                </div>
                <div class="summery-count mt-2">
                    <h6 class="tx-14">This Month Summary</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-main mb-0 fw-bold"><?=$this->sample['totalVisit']??0?></h5>
                                    <p class="mb-0 tx-14">This Month Visit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 d-none">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-main mb-0 fw-bold">2</h5>
                                    <p class="mb-0 tx-14">Frequency</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-main mb-0 fw-bold"><?=$this->sample['collectSample']??0?></h5>
                                    <p class="mb-0 tx-14">Sample Collected</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-main mb-0 fw-bold">₹<?=$this->sample['totalAmount']??0?></h5>
                                    <p class="mb-0 tx-14">Payment Collected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer rounded-0 border-top bg-white text-center">
                <a href="index.php?view=webview_blank&redirect=logistics&clientId=<?=$this->utility->encrypt($this->client["id"])?>&selectEmployeeId=<?=$this->utility->encrypt($this->logistic_person["id"])?>&employeeId=<?=$this->utility->encrypt($this->logistic_person["id"])?>" class="text-main text-decoration-none">View All Sales Visits <i class="fa-solid fa-chevron-right ms-1"></i></a>
            </div>
        </div>
        <?php } else {?>
        <div class="card border-0 rounded-0 mb-3">
            <div class="card-body">
                <h6>Logistics Person Assigned</h6>
                <div class="d-flex align-items-center">
                    <div>
                        <?php
                            $image=SERVER_ROOT."/uploads/user.png";;
                        ?>
                        <img src="<?=$image?>" alt="" class="avtar-sm">
                    </div>
                    <div class="ms-2">
                        <h6 class="mb-0 line-clamp-1 tx-16">NA</h6>
                        <p class="mb-0 content-color line-clamp-1">NA</p>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </section>

</main>
<script src="assets/js/bootstrap.bundle.min.js"></script>