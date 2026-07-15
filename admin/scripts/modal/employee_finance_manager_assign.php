<style>
  .modal-body {
    max-height: 60vh;
    overflow-y: auto;
  }

  .modal-content {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .modal-footer {
    position: sticky;
    bottom: 0;
    background: white;
    border-top: 1px solid #dee2e6;
    z-index: 10;
  }

  .modal-header {
    background-color: #f7f7f7;
  }

  .employee-item:hover {
    background-color: #f5f5f5;
  }
</style>

<?php
$id=$app->getGetVar('id');
if($id!='')
{
  # GET EMPLOYEE NAME ONLY
  $obj_finance_manager = $app->load_model("employee_journey_finance_manager");
  $obj_finance_manager->join_table("employee", "left", array("name"), array("employee_id"=>"id"));
	$rs_finance_manager = $obj_finance_manager->execute("SELECT", false, "", "employee_journey_finance_manager.id='".$id."'");

  # NOT SHOW IF EMPLOYEE IS FINANCE
  $obj_finance_all = $app->load_model("employee_journey_finance_manager");
	$rs_finance_all = $obj_finance_all->execute("SELECT", false, "", "status!='Trash'");
  $financeManager = array_column($rs_finance_all, 'employee_id');

  # GET OLD CHECKED SAME EMP ID
  $obj_finance_submanager = $app->load_model("employee_journey_finance_submanager");
  $obj_finance_submanager->join_table("employee_journey_finance_manager", "left", array(), array("employee_journey_finance_manager_id"=>"id"));
	$rs_finance_submanager = $obj_finance_submanager->execute("SELECT", false, "", "employee_journey_finance_manager.status!='Trash' and employee_journey_finance_submanager.employee_journey_finance_manager_id='".$id."'");
  $assignedEmployeeIds = array_column($rs_finance_submanager, 'employee_id');

  # OTHER FINANCE ASSINED
  $obj_other_assign = $app->load_model("employee_journey_finance_submanager");
  $obj_other_assign->join_table("employee_journey_finance_manager", "left", array(), array("employee_journey_finance_manager_id"=>"id"));
	$rs_obj_other_assign = $obj_other_assign->execute("SELECT", false, "", "employee_journey_finance_manager.status!='Trash' and employee_journey_finance_submanager.employee_journey_finance_manager_id!='".$id."'");
  $assignedOtherEmployeeIds = array_column($rs_obj_other_assign, 'employee_id');

  # EMPLOYEE LIST
	$obj_brand = $app->load_model("employee");
  $obj_brand->join_table("city", "left", array(), array("city_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "employee.id!='".$rs_finance_manager[0]['employee_id']."'");
}
?>
<div class="modal fade" id="modal_employee_finance_manager_assign_addedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Assign Manager Under Finance Manager - <?=$rs_finance_manager[0]['employee_name']?></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="finance_manager_assign_form" id="finance_manager_assign_form" enctype="multipart/form-data"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","value"=>$id), "id") ?>
        <div class="form-row">
          <div class="form-group col-md-12">
            <div style="padding: 12px; padding-bottom:0px;">
              <label>Employees</label>
              <input type="text" class="form-control mb-3" id="employeeSearch" placeholder="Search employee by name or code...">
            </div>
            <div class="modal-body">
            <?php if (!empty($result)) {
              foreach ($result as $employee) {
                $isChecked = in_array($employee['id'], $assignedEmployeeIds) ? 'checked' : '';
                $checkBoxDis = '';
                $empStatus = '<span class="badge badge-secondary ml-auto">Pending</span>';
                if(in_array($employee['id'], $assignedOtherEmployeeIds)){

                  $obj_financeB = $app->load_model("employee_journey_finance_submanager");
                  $obj_financeB->join_table("employee_journey_finance_manager", "left", array(), array("employee_journey_finance_manager_id"=>"id"));
                  $rs_finance_assign = $obj_financeB->execute("SELECT", false, "", "employee_journey_finance_manager.status!='Trash' and employee_journey_finance_submanager.employee_id='".$employee['id']."'");

                  $empStatus = '<span class="badge badge-success ml-auto" style="font-size: 12px;">Assigned To '.$rs_finance_assign[0]['employee_journey_finance_manager_employee_name'].'</span>';
                  $checkBoxDis = 'disabled';
                }
                if(in_array($employee['id'], $financeManager)) {
                  continue;
                }
                ?>
                <label for="employee_<?= $employee['id'] ?>" class="employee-item" data-search="<?= strtolower($employee['name'] . ' ' . $employee['lms_employee_code']) ?>" style="display: flex; align-items: center; cursor: pointer; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 7px;user-select: none;">
                  <input type="checkbox" name="employee_ids[]" id="employee_<?= $employee['id'] ?>" value="<?= $employee['id'] ?>" style="margin-right: 10px; flex-shrink: 0;" <?= $isChecked ?> <?=$checkBoxDis?>>
                  <?=$employee['name']?> - <?=$employee['lms_employee_code']?> <?php if(!empty($employee['city_name'])) { echo '('.$employee['city_name'].')'; }?> <?=$empStatus?>
                </label>
                <h4 id="noResultsMsg" class="text-danger" style="display:none;">No results found for "<span id="searchText"></span>".</h4>
            <?php } } ?>
          </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 submit_btn finance_manager_assign_modal_submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('#employeeSearch').on('keyup', function() {
      var value = $(this).val().toLowerCase();
      var matchCount = 0;

      $('.employee-item').each(function() {
        var searchText = $(this).data('search');
        if (searchText.indexOf(value) !== -1) {
          $(this).show();
          matchCount++;
        } else {
          $(this).hide();
        }
      });

      if (matchCount === 0 && value.trim() !== '') {
        $('#searchText').text(value);
        $('#noResultsMsg').show();
      } else {
        $('#noResultsMsg').hide();
      }
      
    });
  });
</script>