<?php
$id=$app->getGetVar('id');
if($id!='')
{
	$obj_brand = $app->load_model("master_centre");
	$result = $obj_brand->execute("SELECT", false, "", "id='".$id."'");
	$id=$result[0]['id'];
	$name=$result[0]['name'];
  $lms_center_id=$result[0]['lms_center_id'];
  $center_type=$result[0]['center_type'];
  $address=$result[0]['address'];
  $cityzone=$result[0]['cityzone'];
  $area=$result[0]['area'];
  $mobile=$result[0]['mobile'];
  $contact_person=$result[0]['contact_person'];
  $contact_mobile=$result[0]['contact_mobile'];
  $payment_mode=$result[0]['payment_mode'];
	$status=$result[0]['status'];
  $sort_order=$result[0]['sort_order'];

  $city_id=$result[0]['city_id'];
  $state_id=$result[0]['state_id'];
  $master_businesszone_id=$result[0]['master_businesszone_id'];
  $employee_id=$result[0]['employee_id'];

  $obj_city = $app->load_model("city");
  $cityR = $obj_city->execute("SELECT", false, "", "status='Active' and state_id='".$result[0]['state_id']."'","name ASC");
  $city=array_column($cityR,'name', 'id');

}

$obj_master_businesszone = $app->load_model("master_businesszone");
$master_businesszone = $obj_master_businesszone->execute("SELECT", false, "", "status='Active'","name ASC");
$businesszone=array_column($master_businesszone,'name', 'id');

$obj_state = $app->load_model("state");
$stateR = $obj_state->execute("SELECT", false, "", "status='Active'","name ASC");
$state=array_column($stateR,'name', 'id');

$obj_employee = $app->load_model("employee");
$obj_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
$employeeR = $obj_employee->execute("SELECT", false, "", "employee.status='Active' and master_designation.name='Logistics Manager'","employee.name ASC");
$employee['']='Select Logistic Manager';
foreach($employeeR as $item) {
  $employee[$item['id']]=$item['name'];
}

?>
<div class="modal fade" id="modal_master_centre_addedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Centre Form</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="master_centre_form" id="master_centre_form"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","class"=>"form-control","value"=>$id), "id") ?>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail4">Name<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$name,"required"=>""), "name");?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">centre ID<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$lms_center_id,"required"=>""), "lms_center_id");?>
            </div>

            <div class="form-group col-md-4">
              <label for="inputEmail4">Center Type<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$center_type,"required"=>""), "center_type");?>
            </div>

            <div class="form-group col-md-4">
              <label for="inputEmail4">Cityzone<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$cityzone,"required"=>""), "cityzone");?>
            </div>

            <div class="form-group col-md-4">
              <label for="inputEmail4">Area<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$area,"required"=>""), "area");?>
            </div>

            <div class="form-group col-md-12">
              <label for="inputEmail4">Address<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$address,"required"=>""), "address");?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Mobile<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$mobile,"required"=>""), "mobile");?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Payment Mode</label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$payment_mode), "payment_mode");?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Contact Person<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$contact_person,"required"=>""), "contact_person");?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Contact Person Mobile<span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control ","value"=>$contact_mobile,"required"=>""), "contact_mobile");?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Sort Order</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control ","selected"=>$sort_order, "values"=>$app->utility->sort_order('master_centre'),"required"=>""), "sort_order") ;?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Status</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control","selected"=>$status, "values"=>array("Active"=>"Active","Inactive"=>"Inactive"),"required"=>""), "status") ;?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">State</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$state_id, "values"=>$state, "onchange"=>"getCityList(this.value)"), "state_id") ;?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">City</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$city_id, "values"=>$city), "city_id") ;?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Business Zone</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$master_businesszone_id, "values"=>$businesszone), "master_businesszone_id") ;?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Logistic Manager</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$employee_id, "values"=>$employee), "employee_id") ;?>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 submit_btn master_centre_modal_submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
