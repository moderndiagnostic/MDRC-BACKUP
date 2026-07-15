<?php
$id = $app->getGetVar('id');
$purpose = $app->getGetVar('purpose');
$name = $app->getGetVar('name');
?>
<div class="modal fade" id="modal_view_visit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2"><?=$name?> - Visits</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <? $app->htmlBuilder->buildTag("form", array("data-parsley-validate" => ""), "product_form"); ?>
      <? $app->htmlBuilder->buildTag("input", array("type" => "hidden", "class" => "form-control","value"=>$id), "id") ?>
      <? $app->htmlBuilder->buildTag("input", array("type" => "hidden", "class" => "form-control","value"=>$purpose), "purpose") ?>
      <div class="modal-body">
        
          <div class="df-example demo-table">
            <table id="table_employee_visit" class="table">
              <thead>
                <tr>
                  <th class="wd-5p">ID.</th>
                  <th class="wd-15p">Employee</th>
                  <th class="wd-15p">Client </th>
                  <th class="wd-15p">Pickup</th>
                  <th class="wd-20p">Summary</th>
                </tr>
              </thead>
            </table>
          </div>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>