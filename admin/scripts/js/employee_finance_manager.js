// ==================== START : LOAD DATATABLE ====================
if ($("#table_employee_journey_finance_manager").length > 0) {
	$(document).ready(function(){
		$('#table_employee_journey_finance_manager').DataTable({
			"order": [[ 0, "desc" ]],
			"columnDefs": [ { 'targets': [2],"orderable": false } ],
			'autoWidth':false,
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url':'scripts/ajax/index.php?method=employee_finance_manager&actionType=employee_finance_manager_list',
				"data": function(d) {
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.actionType = "employee_list";  
				},
			},
			'columns': [
				{ "data": "id" },
				{ "data": "employee_name" },
				{ "data": "manager_count" },
				{ "data": "created_at" },
				{ "data": "btn" }
			],
			language: 
			{
				searchPlaceholder: 'Search...',
				sSearch: '',
				lengthMenu: '_MENU_ items/page',
			}
		});
		
		$('.dataTables_length select').select2({ minimumResultsForSearch: Infinity });
		$('body').tooltip({selector: '[data-toggle="tooltip"]'});
	});
}

function search_data() {
	load_datatable();
}

function reset_data() {
	$("#start_date").val('');
	$("#end_date").val('');
	load_datatable();
}

$(document).on("click",".tab_link", function ()
{
	$('.tab_link').removeClass('active');
	$(this).addClass("active");
	$("#tab_filter").val($(this).html());
	load_datatable();
});

function load_datatable()
{
	var oTable = $('#table_employee_journey_finance_manager').dataTable();
	oTable.api().ajax.reload(null, false);
}
// ==================== END : LOAD DATATABLE ====================



// ==================== START : ADDEDIT FINANCE MANAGER MODAL OPEN ====================
$(document).on("click",".employee_finance_manager_addedit_onclick", function ()
{
	getId=$(this).data("id");
	$('#custom_ajax_preloader').show();
	$.ajax({
	type: 'POST',
	url: 'scripts/modal/index.php?method=employee_finance_manager_addedit&id='+getId,
	dataType : 'html',
	data: $(this).serialize()
	})
	.done(function(data)
	{
		// show the response
		$('#ajax_modal_container').html(data);
		$('#modal_finance_manager_addedit').modal('show');
		$('#custom_ajax_preloader').hide();
		$('#employee_form').parsley();
		$.getScript("scripts/js/ajax.js");
	})
	.fail(function()
	{
		// just in case posting your form failed
		alert( "Try again." );
		$('#custom_ajax_preloader').hide();
	});
});
// ==================== END : ADDEDIT FINANCE MANAGER MODAL OPEN ====================



// ==================== START : ADDEDIT FINANCE MANAGER SUBMIT ====================
$(document).on("click",".finance_manager_modal_submit", function ()
{
    $('#employee_finance_manager_form').validate({
		submitHandler: function (form)
		{
			$('.finance_manager_modal_submit').html('<span class="spinner-border spinner-border-sm mg-r-5" role="status" aria-hidden="true"></span> Loading...');
			$(".finance_manager_modal_submit").attr("disabled", true);
			var dataString = new FormData(form);
			dataString.append('method', 'employee_finance_manager');
			dataString.append('actionType', 'financeManagerAddEdit');
			$.ajax({
                dataType: 'json',
                type: "POST",
				url: "scripts/ajax/index.php",
				data: dataString,
				cache:false,
          	  	contentType: false,
           	 	processData: false,
	 			success: function (responseData)
				{
					$('.finance_manager_modal_submit').html('Submit');
					$(".finance_manager_modal_submit").attr("disabled", false);
				
					if(responseData.RESULT==1)
					{
						$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
					}
					else if(responseData.RESULT==0)
					{
						$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'success',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
						$('#modal_finance_manager_addedit').modal('hide');
						var oTable = $('#table_employee_journey_finance_manager').dataTable( );
						oTable.api().ajax.reload(null, false); 
					}
                },
                error: function (responseData) {
                    console.log('Ajax request not recieved!');
                }
            });
            return false;
        }
    });
});
// ==================== END : ADDEDIT FINANCE MANAGER SUBMIT ====================



// ==================== START : DELETE FINANCE MANAGER ====================
$(document).on("click",".employee_finance_manager_delete_onclick", function ()
{
	var getid=$(this).data('id');
	if(getid!='')
	{
		swal({
			title: "Are you sure?",
			text: "You will not be able to undo after this action!",
			type: "warning",
			showCancelButton: true,
			cancelButtonClass: 'btn-primary',
			confirmButtonClass: 'btn-warning',
			confirmButtonText: "Yes, delete it!",
			confirmButtonClass: "confirm btn btn-lg btn-warning xyz",
			closeOnConfirm: true
		}, function (r)
		{
			if(r == true)
			{
				$.ajax({
						type: "POST",
						dataType: 'json',
						url: "scripts/ajax/index.php",
						data: "method=employee_finance_manager&actionType=employeeFinanceManagerDelete&getid="+getid,
						success: function(responseData)
						{
						if(responseData.RESULT==0)
						{
						var oTable = $('#table_employee_journey_finance_manager').dataTable( );
						oTable.api().ajax.reload(null, false);
						$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>Record Deleted Successfully.</p>', {type:'warning',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
						return false;
						}
						else
						{
							swal({ title: "Try Again...",
							text: data.msg,
							type: "warning",
							timer: 1000
							});
							return false;
						}
					}
				});
			}
			else
			{
				return false;
			}
		});
	}
	else
	{
		swal({ 
			title: "Try Again...",
			text: "Oops Something gone wrong...",
			type: "warning",
			timer: 1500
		});
		return false;
	}
});
// ==================== END : DELETE FINANCE MANAGER ====================

$(document).on("click",".employee_finance_manager_assign_onclick", function ()
{
	getId=$(this).data("id");
	$('#custom_ajax_preloader').show();
	$.ajax({
		type: 'POST',
		url: 'scripts/modal/index.php?method=employee_finance_manager_assign&id='+getId,
		dataType : 'html',
		data: $(this).serialize()
	})
	.done(function(data)
	{
		// show the response
		$('#ajax_modal_container').html(data);
		$('#modal_employee_finance_manager_assign_addedit').modal('show');
		$('#custom_ajax_preloader').hide();
		$('#finance_manager_assign_form').parsley();
		$.getScript("scripts/js/ajax.js");
	})
	.fail(function()
	{
		// just in case posting your form failed
		alert( "Try again." );
		$('#custom_ajax_preloader').hide();
	});
});

$(document).on("click",".finance_manager_assign_modal_submit", function ()
{
    $('#finance_manager_assign_form').validate({
		submitHandler: function (form)
		{
			$('.finance_manager_assign_modal_submit').html('<span class="spinner-border spinner-border-sm mg-r-5" role="status" aria-hidden="true"></span> Loading...');
			$(".finance_manager_assign_modal_submit").attr("disabled", true);
			var dataString = new FormData(form);
			dataString.append('method', 'employee_finance_manager');
			dataString.append('actionType', 'employeeFinanceAssignAddEdit');
			$.ajax({
                dataType: 'json',
                type: "POST",
				url: "scripts/ajax/index.php",
				data: dataString,
				cache:false,
          	  	contentType: false,
           	 	processData: false,
	 			success: function (responseData)
				{
					$('.finance_manager_assign_modal_submit').html('Submit');
					$(".finance_manager_assign_modal_submit").attr("disabled", false);
				
					if(responseData.RESULT==1)
					{
						$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
					}
					else if(responseData.RESULT==0)
					{
						$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'success',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
						$('#modal_employee_finance_manager_assign_addedit').modal('hide');
						var oTable = $('#table_employee_journey_finance_manager').dataTable( );
						oTable.api().ajax.reload(null, false); 
					}
                },
                error: function (responseData) {
                    console.log('Ajax request not recieved!');
                }
            });
            return false;
        }
    });
});