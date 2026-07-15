<?php
$id=$app->getGetVar('id');
if($id!='')
{
	$obj_brand = $app->load_model("client_logistic_assign");
	$result = $obj_brand->execute("SELECT", false, "", "id='".$id."'");
	$id=$result[0]['id'];
	$name=$result[0]['name'];
	$status=$result[0]['status'];
  $sort_order=$result[0]['sort_order'];
}
?>
<div class="modal fade" id="modal_client_logistic_assign_addedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">client_logistic_assign Form</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="client_logistic_assign_form" id="client_logistic_assign_form"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","class"=>"form-control","value"=>$id), "id") ?>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-8">
              <label for="inputEmail4">client_logistic_assign Name<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$name,"required"=>""), "name");?>
            </div>

            

            <div class="form-group col-md-6">
              <label for="inputEmail4">Sort Order</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control ","selected"=>$sort_order, "values"=>$app->utility->sort_order('client_logistic_assign'),"required"=>""), "sort_order") ;?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Status</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control","selected"=>$status, "values"=>array("Active"=>"Active","Inactive"=>"Inactive"),"required"=>""), "status") ;?>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 submit_btn client_logistic_assign_modal_submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
