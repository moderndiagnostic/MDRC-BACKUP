<?php
$id=$app->getGetVar('id');
if($id!='')
{
	$obj_brand = $app->load_model("payment_links");
  $obj_brand->join_table("employee", "left", array("name","lms_employee_code","email","mobile"), array("employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "payment_links.id='".$id."'");

  $obj_brand = $app->load_model("payment_link_transaction");
	$detail = $obj_brand->execute("SELECT", false, "", "payment_link_id='".$id."'");
  $amount=$app->utility->moneyFormatIndia($result[0]['amount']);
  if($result[0]['status']=='Success')
  {
    $status='<span class="badge badge-success ml-1">Sucess</span>';
  }
  else if($result[0]['status']=='Fail')
  {
    $status='<span class="badge badge-danger ml-1">Sucess</span>';
  }
  else
  {
    $status='<span class="badge badge-warning ml-1">Pending</span>';
  }
}
?>
<div class="modal fade" id="modal_payment_link_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Payment Link Detail</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="payment_link_form" id="payment_link_form"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","class"=>"form-control","value"=>$id), "id") ?>
        <div class="modal-body">
          <div class="form-row df-example">
            <div class="form-group col-md-4">
              <label for="inputEmail4">Name</label>
             <p><?=$result[0]['name']?></p>
            </div>
            <div class="form-group col-md-4">
              <label for="inputEmail4">Mobile</label>
             <p><?=$result[0]['mobile']?></p>
            </div>
            <div class="form-group col-md-4">
              <label for="inputEmail4">Amount</label>
             <p><?=$amount?></p>
            </div>
            <div class="form-group col-md-4">
              <label for="inputEmail4">Create By Employee</label>
             <p><?=$result[0]['employee_name']?><br><b><?=$result[0]['employee_lms_employee_code']?></b></p>
            </div>
            <div class="form-group col-md-4">
              <label for="inputEmail4">Created At</label>
             <p><?=date('d-m-Y H:i:s', strtotime($result[0]['created_at']))?></p>
            </div>
            <div class="form-group col-md-4">
              <label for="inputEmail4">Transaction Status</label>
              <p><?=$status?></p>
            </div>
            <div class="form-group col-md-12">
              <label for="inputEmail4">Remark<span class="tx-danger">*</span></label>
             <p><?=$result[0]['remark']?></p>
            </div>
            <?php if(count($detail)>0){ ?>
            <div class="form-group col-md-12" id="">
              <table class="table table-condensed border">
                <thead>
                  <tr>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Transaction Date</th>
                    <th>Remark</th>
                  </tr>
                </thead>
                <tbody>
                  <?php for($i=0; $i<count($detail); $i++)
                  {
                    if($detail[$i]['status']=='Success')
                    {
                      $status='<span class="badge badge-success ml-1">Sucess</span>';
                    }
                    else if($detail[$i]['status']=='Fail')
                    {
                      $status='<span class="badge badge-danger ml-1">Sucess</span>';
                    }
                    else
                    {
                      $status='<span class="badge badge-warning ml-1">Pending</span>';
                    }
                  ?>
                    <tr>
                      <td>
                        <b><?=$detail[$i]['transaction_id'] ?></b>
                      </td>
                      <td>
                        <?=$status?>
                      </td>
                      <td>
                        <?=date('d-m-Y H:i:s', strtotime($detail[$i]['created_at']))?>
                      </td>
                      <td>
                      <?=$detail[$i]['remark'] ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php } ?>
          </div>
          
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
