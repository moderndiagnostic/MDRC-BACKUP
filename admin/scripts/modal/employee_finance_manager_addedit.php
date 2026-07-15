<?php
$obj_model_city = $app->load_model("city");
$rs = $obj_model_city->execute("SELECT", false,"","status='Active'");
?>
<style>
  .ui-autocomplete {
    z-index: 1051 !important; /* higher than Bootstrap modal (1050) */
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
  }
</style>
<div class="modal fade" id="modal_finance_manager_addedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content tx-14">
      <div class="modal-header">
        <h6 class="modal-title" id="exampleModalLabel2">Employee Finance Manager Form</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <form method="post" name="employee_finance_manager_form" id="employee_finance_manager_form" enctype="multipart/form-data"  data-parsley-validate>
        <? $app->htmlBuilder->buildTag("input", array("type"=>"hidden","class"=>"form-control","value"=>$id), "id") ?>
        <div class="modal-body">
          <div class="form-row">

            <div class="form-group col-md-12">
              <label for="inputEmail4">Employee <span class="tx-danger">*</span> </label>
              <input name="employee_search" id="employee_search" type="text" class="form-control" placeholder="Search Employee" autocomplete="off">
              <ul id="selected_employees" style="list-style:none; padding-left:0; margin-top:10px;"></ul>
            </div>
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary tx-13 submit_btn finance_manager_modal_submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>

$("#employee_search").on("focus", function () {
  if (!$(this).prop('readonly')) {
		$(this).autocomplete("search", " ");
	}
});

$("#employee_search").autocomplete({
	source: function (request, response) {
		$.ajax({
			url: "scripts/ajax/index.php",
			type: 'GET',
			cache: false,
			dataType: "json",
			data: {
				method: 'employee_finance_manager',
				actionType: 'employeeSearch',
				search: request.term,
			},
			success: function (data) {
				response(data);
			}
		});
	},
	select: function (event, ui) {
    console.log("Autocomplete selected item:", ui.item);

    if (!ui.item) return false;

    // Check if this employee is already selected
    if ($("#selected_employees input[value='" + ui.item.id + "']").length > 0) {
      $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>Employee already selected!</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
      return false;
    }

    // Create the li with design and hidden input
    let li = $(
      `<li style="padding:5px 10px; background:#e9ecef; margin-bottom:5px; border-radius:4px; display:flex; align-items:center; justify-content:space-between;">
        <span>${ui.item.label}</span>
        <button type="button" class="remove-employee btn btn-sm btn-danger" style="margin-left:10px;">&times;</button>
        <input type="hidden" name="selected_id[]" value="${ui.item.id}">
      </li>`
    );

    // Append to ul
    $("#selected_employees").append(li);

    // Clear input so user can select another
    $("#employee_search").val("");
    return false;
	}
});

$(document).on("click", ".remove-employee", function() {
  $(this).closest("li").remove();
});
</script>