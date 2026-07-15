<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

<main class="mb-xxl">
<section>
        <div class="timeline-item">
            <?php if(count($this->topEmployee)>0) { 
                $btn=false;
                foreach($this->topEmployee as $topEmployee){
                    $class='';
                    $url='index.php?view=webview_my_team&employeeID='.$this->utility->encrypt($topEmployee['id']).'&employeePhone=&level='.(!empty($this->getGetVar('level'))?$this->getGetVar('level'):0).'&selectEmployeeId=';
                    if($topEmployee['id']==$this->utility->decrypt($this->getGetVar('employeeID')))
                    {
                        $btn=true;
                        $class='self';
                    }
                ?>
                <div class="bg-white mb-2 <?=$class?>">
                    <div class="d-flex justify-content-between p-3 py-2 align-items-center ">
                        <h6 class="mb-0"><?=$topEmployee['name']?><span class="tx-14 content-color"><?=$topEmployee['lms_employee_code']?></span></h6>
                        <?php if($btn) { ?>
                        <div>
                            <a href="<?=$url.$this->utility->encrypt($topEmployee['id'])?>" class="card-anchor">
                                <div class="arrow-box-outline">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </div>
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php
                    
             } 
            }
            ?>
            <div class="team-view card border-0 rounded-0 border-bottom mb-2">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <?php 
                            if(!empty($this->employee['image'])){
                                $image=$this->utility->get_image_url($this->employee['image'],'employee','large');
                            }else{
                                $image= SERVER_ROOT."/uploads/user.png";
                            }
                        ?>
                        <?php if(count($this->topEmployee)>0) { ?>
                            <div>
                                <img src="assets/images/svg/arrow-turn-down-right.svg" alt="">
                            </div>
                        <?php } ?>
                        <div class="ms-2">
                            <h6 class="text-main mb-0"><?=$this->employee['name']; ?></h6>
                            <p class="mb-0 content-color"><?=$this->employee['master_designation_name']; ?></p>
                            <span class="fw-bold tx-12"><?=$this->employee['lms_employee_code']; ?></span>
                        </div>
                        <div class="ms-auto">
                            <img src="<?=$image?>" alt="" class="avtar-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="main-wrap pt-3">
    <form method="post" action="" name="teamSearchForm" class="teamSearchForm">
        <h6 class="text-main">Total <span id="totalClient"></span> Client Found</h6>
        <div class="search-box">
            <div>
                <img src="assets/images/svg/search.svg" alt="" class="search">
                <input class="form-control" id="search" name="search" type="text" placeholder="Search here..." autocomplete="off">
            </div>
        </div>
        <input type="hidden" name="selectEmployeeId" value="<?php echo !empty($this->getGetVar('selectEmployeeId'))?$this->getGetVar('selectEmployeeId'):$this->getGetVar('employeeID'); ?>" />
        <input type="hidden" name="employeeID" value="<?php echo !empty($this->getGetVar('employeeID'))?$this->getGetVar('employeeID'):''?>" /> 
    </form>
        <div class="row">
            <div class="col-12 teamListHtml">
            </div>   
        </div>
        <div class="row">
			<div class="col-12 text-center">
				<a href="javascript:void(0);" class="btn btn-primary" id="loadMore">
					Load More
				</a>
			</div>
		</div>
    </section>
</main>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>


$(document).ready(function() {
    // Initialize the page counter and limit
    var page = 0;
    var limit = 20;

    // Function to load data
    function loadClientList() {
        // Serialize form data
        var formData = $(".teamSearchForm").serialize();
        formData += '&method=clientList';
        formData += '&page=' + page + '&limit=' + limit; // Add pagination parameters

        $.ajax({
            url: 'scripts/ajax/index.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.RESULT == "1") {
                    if (page === 0) {
                    $(".teamListHtml").html(response.html); // Replace on first page load
                    } else {
                        $(".teamListHtml").append(response.html); // Append on subsequent page loads
                    }
                    // Show/Hide "Load More" button based on the number of items
                    $('#totalClient').html(response.totalItems);
                    if (response.totalItems <= (page+1) * limit) {
                        $("#loadMore").hide();  // No more items to load
                    } else {
                        $("#loadMore").show();  // More items to load
                    }
                } else {
                    $("#loadMore").hide(); // No more data
                    //$(".teamListHtml").html(response.msg)
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    }

    // Load initial data on page load
    loadClientList();

    // Handle load more button click
    $("#loadMore").click(function() {
        page++; // Increment page counter
        loadClientList(); // Load next set of data
    });
    $('#search').on('keyup', function() {
        var page = 0;
        var limit = 10;
        loadClientList();
    });
});
</script>