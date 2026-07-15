<?php
$image='assets/img/profile.png';

$id=$app->getGetVar('id');

$lmsEmployee=false;
if($id!='')
{
	//Edit employee
	$obj_brand = $app->load_model("employee");
  $obj_brand->join_table("employee_detail", "left", array(), array("id"=>"employee_id"));
	$result = $obj_brand->execute("SELECT", false, "", "employee.id='".$id."'");			
	
  $name=$result[0]['name'];
  $lms_employee_code=$result[0]['lms_employee_code'];
  $mobile=$result[0]['mobile'];
  $login_password=$result[0]['login_password'];
	$email=$result[0]['email'];
  $master_designation_id=$result[0]['master_designation_id'];
  $status=$result[0]['status'];
  $city_id=$result[0]['city_id'];
  $area=$result[0]['employee_detail_area'];
  $reporting_employee_lms_id=$result[0]['reporting_employee_lms_id'];

	$image=$app->utility->get_image_path($result[0]["image"],'employee','large');
 
  $lmsEmployee=$result[0]['lms_employee_id']>0?true:false;
}

if($result[0]["image"]!='') {
	$file_class="fileupload-exists";
} else {
	$file_class="fileupload-new";
}

$obj_model_city = $app->load_model("city");
$rs = $obj_model_city->execute("SELECT", false,"","status='Active'");
$records1 = array();
$records1[''] = " Select City";
for($i=0;$i<count($rs);$i++){
  $records1[$rs[$i]['id']] = $rs[$i]['name'];
}

$obj_model_designation = $app->load_model("master_designation");
$rs = $obj_model_designation->execute("SELECT", false,"","status='Active'");
$records_designation = array();
$records_designation[''] = " Select Designation";
for($i=0;$i<count($rs);$i++){
  $records_designation[$rs[$i]['id']] = $rs[$i]['name'];
}

if($id!='')
{
  $mCond=" and employee.id!='".$id."'";
}
$obj_model_employee = $app->load_model("employee");
$obj_model_employee->join_table("master_designation", "left", array(), array("master_designation_id"=>"id"));
$rs = $obj_model_employee->execute("SELECT", false,"","employee.status='Active'".$mCond);
$records_emp = array();
$records_emp[''] = " Select Employee";
for($i=0;$i<count($rs);$i++){
  $records_emp[$rs[$i]['id']] = $rs[$i]['name'].' ( '.$rs[$i]['master_designation_name'].' ) ';
}
?>

<?php if($lmsEmployee) { ?>
  
<div class="modal fade" id="modal_employee_addedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Employee Form</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="employee_form" id="employee_form" enctype="multipart/form-data"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","class"=>"form-control","value"=>$id), "id") ?>
        <div class="modal-body">
          <div class="form-row">

            <div class="form-group col-md-12">
              <label for="inputEmail4">Name <span class="tx-danger">*</span> </label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$name,"readonly"=>"readonly","required"=>""), "name") ?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Profile Photo <span class="tx-danger">*</span> </label>
              
              <div class="fileupload <?=$file_class;?>" data-provides="fileupload">
                <div class="fileupload-new" > <img src="assets/img/profile.png" class="up_img"> </div>
                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 150px; line-height: 20px;"> <img src="<?=$image;?>" />  </div>
                <div>
                	  <span class="btn btn-file btn-default"> 
                    	<span class="fileupload-new btn btn-white btn-xs">Select image</span>
                    	<span class="fileupload-exists btn btn-white btn-xs">Change</span>
                    	<? $app->htmlBuilder->buildTag("input", array("type"=>"file","class"=>""), "imageFile") ?>
                    </span> 
                    <a href="#" class="btn btn-xs fileupload-exists btn-white" data-dismiss="fileupload">Remove</a> 
                </div>
                <div class="tx-12">(only Jpeg,jpg,png. Size Below 2 MB)</div>
              </div>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Password <span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$login_password,"required"=>""), "login_password") ?>
              <br/>
              <label for="inputEmail4">LMS Employee Code </label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$lms_employee_code,"readonly"=>"readonly"), "lms_employee_code") ?>
              <br/>
              <label for="inputEmail4">1 KM Price </label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control numbersOnly","value"=>$result[0]['employee_detail_per_km']??''), "per_km") ?>
            </div>

            <div class="form-group col-md-12">
              <label for="inputEmail4">Status</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control","selected"=>$status, "values"=>array("Active"=>"Active","Inactive"=>"Inactive"),"required"=>""), "status") ;?>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 submit_btn employee_modal_submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
  
<?php } else { ?>

<div class="modal fade" id="modal_employee_addedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Employee Form</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="employee_form" id="employee_form" enctype="multipart/form-data"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","class"=>"form-control","value"=>$id), "id") ?>
        <div class="modal-body">
          <div class="form-row">

            <div class="form-group col-md-6">
              <label for="inputEmail4">Name <span class="tx-danger">*</span> </label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$name,"required"=>""), "name") ?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Mobile </label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$mobile), "mobile") ?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Profile Photo <span class="tx-danger">*</span> </label>
              
              <div class="fileupload <?=$file_class;?>" data-provides="fileupload">
                <div class="fileupload-new" > <img src="assets/img/profile.png" class="up_img"> </div>
                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 150px; line-height: 20px;"> <img src="<?=$image;?>" />  </div>
                <div>
                	  <span class="btn btn-file btn-default"> 
                    	<span class="fileupload-new btn btn-white btn-xs">Select image</span>
                    	<span class="fileupload-exists btn btn-white btn-xs">Change</span>
                    	<? $app->htmlBuilder->buildTag("input", array("type"=>"file","class"=>""), "imageFile") ?>
                    </span> 
                    <a href="#" class="btn btn-xs fileupload-exists btn-white" data-dismiss="fileupload">Remove</a> 
                </div>
                <div class="tx-12">(only Jpeg,jpg,png. Size Below 2 MB)</div>
              </div>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Email </label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$email), "email") ?>

              <label for="inputEmail4" style="margin-top: 15px;">LMS Employee Code <span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$lms_employee_code,"required"=>""), "lms_employee_code") ?>

              <label for="inputEmail4" style="margin-top: 15px;">Designation <span class="tx-danger">*</span> </label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$master_designation_id, "values"=>$records_designation,"required"=>""), "master_designation_id") ;?>
            </div>
   
           <div class="form-group col-md-6">
              <label for="inputEmail4">City <span class="tx-danger">*</span> </label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$city_id, "values"=>$records1,"required"=>""), "city_id") ;?>
            </div>
              
            <div class="form-group col-md-6">
              <label for="inputEmail4">Password <span class="tx-danger">*</span></label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control","value"=>$login_password,"required"=>""), "login_password") ?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Area</label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control", "value"=>$area), "area") ;?>
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail4">Status</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control","selected"=>$status, "values"=>array("Active"=>"Active","Inactive"=>"Inactive"),"required"=>""), "status") ;?>
            </div>

            <div class="form-group col-md-12">
              <label for="inputEmail4">Reporting To</label>
              <? $app->htmlBuilder->buildTag("select", array("class"=>"form-control select2","selected"=>$reporting_employee_lms_id, "values"=>$records_emp), "reporting_employee_lms_id") ;?>
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail4">1 KM Price</label>
              <? $app->htmlBuilder->buildTag("input", array("type"=>"text","class"=>"form-control numbersOnly","value"=>$result[0]['employee_detail_per_km']??''), "per_km") ;?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 submit_btn employee_modal_submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php } ?>

