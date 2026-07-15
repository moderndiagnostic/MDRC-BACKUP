<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <title>Payment Success</title>
     
</head>

<body style="background-color: #eeeeee; overflow-x: hidden;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 px-0">
                <div class="card p-3 vh-100">
                    <img src="https://www.mdrcindia.com/images/logo.svg" alt="" class="img-fluid" height="190px" width="190px">
                    <div class="payment-text mt-4">
                        <h2><i class="fa-regular fa-circle-xmark fs-2 text-danger"></i> ₹ <?=$this->payment['payment_links_amount']?></h2>
                        <p class="mb-0 text-gray mt-4">Transaction Number</p>
                        <p class="fw-bold mb-2 fs-5"># <?=$this->payment['id']?></p>
                        <p class="mb-0 text-gray">Transaction ID</p>
                        <p class="fw-bold mb-2 fs-5"><?=$this->payment['transaction_id']?></p>
                    </div>
                    <div class="btn-rows mt-3">
                        <a href="<?=SERVER_ROOT.'/qr-payment/'.$this->payment['payment_links_id'].''?>" class="btn btn-danger rounded-pill px-3 py-2">Retry Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>