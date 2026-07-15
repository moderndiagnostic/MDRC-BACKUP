<?php
$image='assets/img/profile.png';
$id=$app->getGetVar('id');
$lmsEmployee=false;
if($id!='')
{
  // FETCH DATA
	$obj_brand = $app->load_model("employee_leave");
  $obj_brand->join_table("employee", "left", array(), array("employee_id"=>"id"));
  $obj_brand->join_table(["employee"=>"employeeU"], "left", array(), array("update_by_employee_id"=>"id"));
	$result = $obj_brand->execute("SELECT", false, "", "employee_leave.id='".$id."'");
  // DEFINE IN VARIABLE
  $leave_start= date('d-m-Y', strtotime($result[0]['leave_start']));
  $leave_end= date('d-m-Y', strtotime($result[0]['leave_end']));
  $created_at= date('d-m-Y h:i A', strtotime($result[0]['created_at']));
  $status_updated_at= date('d-m-Y h:i A', strtotime($result[0]['status_updated_at']));
  $status=$app->utility->get_employee_leave_status(["status"=>$result[0]["status"]]);
}

?>
<!-- MODAL -->
<div class="modal fade" id="modal_employee_leave_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Employee Leave Detail</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div class="container">
            <div class="row">
              <div class="col-xl">
                <lable>Name</lable>
                <p class="tx-semibold"><?=$result[0]['employee_name'];?>  (<?=$result[0]['employee_lms_employee_code'];?>)</p>
              </div>
              <div class="col-xl">
                <lable>Reason</lable>
                <p class="tx-semibold"><?=$result[0]['reason']; ?></p>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
              <div class="col-xl">
                <lable>Leave Start Date</lable>
                <p class="tx-semibold"><?=$leave_start; ?></p>
              </div>
              <div class="col-xl">
                <lable>Leave End Date</lable>
                <p class="tx-semibold"><?=$leave_end; ?></p>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
              <div class="col-xl">
                <lable>Requested Date</lable>
                <p class="tx-semibold"><?=$created_at; ?></p>
              </div>
              <div class="col-xl">
                <lable>Status</lable>
                <p class="tx-semibold"><?=$status['badge']; ?></p>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
                  <?php if($result[0]['employeeU_name']!=''){ ?>
              <div class="col-xl">
                <lable>Approved By</lable>
                <p class="tx-semibold"><?=$result[0]['employeeU_name']; ?>  (<?=$result[0]['employeeU_lms_employee_code'];?>)</p>
              </div>
                  <?php } if($result[0]['update_remark']!=''){ ?>
              <div class="col-xl">
                <lable>Remark</lable>
                <p class="tx-semibold"><?=$result[0]['update_remark']; ?></p>
              </div>
              <?php } if($result[0]['update_remark']!='' && $result[0]['employeeU_name']!=''){?>
            </div>
          </div>
          <div class="container">
            <div class="row">
                  <?php } if($result[0]['status_updated_at']!=''){ ?>
              <div class="col-xl">
                <lable>Updated</lable>
                <p class="tx-semibold"><?=$status_updated_at; ?></p>
              </div>
               <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>