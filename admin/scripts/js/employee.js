if ($("#table_employee").length > 0) {
$(document).ready(function(){
	$('#table_employee').DataTable({
		"order": [[ 0, "desc" ]],
		"columnDefs": [ { 'targets': [5],"orderable": false } ],
  		'autoWidth':false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'ajax': {
			'url':'scripts/ajax/index.php?method=employee&actionType=employee_list',
			"data": function(d) {
				d.start_date = $('#start_date').val();
				d.end_date = $('#end_date').val();
				d.designation_id = $('#designation_id').val();
				d.tab_filter = $('#tab_filter').val();
				d.actionType = "employee_list";     
			  },
		},
		'columns': [
		  	{ "data": "id" },
            { "data": "name" },
			{ "data": "email" },
			{ "data": "reporting" },
			{ "data": "status" },
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
	//$("#due_start_date").val('');
	//$("#due_end_date").val('');
	//$("#select_purchase_id option").prop("selected", false).trigger( "change" );
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
	var oTable = $('#table_employee').dataTable();
	oTable.api().ajax.reload(null, false);
}


$(document).on("click",".employee_addedit_onclick", function ()
{
	getId=$(this).data("id");
	$('#custom_ajax_preloader').show();
	$.ajax({
	type: 'POST',
	url: 'scripts/modal/index.php?method=employee_addedit&id='+getId,
	dataType : 'html',
	data: $(this).serialize()
	})
	.done(function(data)
	{
		// show the response
		$('#ajax_modal_container').html(data);
		$('#modal_employee_addedit').modal('show');
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

$(document).on("click",".employee_modal_submit", function ()
{
    $('#employee_form').validate({
		submitHandler: function (form)
		{
			$('.employee_modal_submit').html('<span class="spinner-border spinner-border-sm mg-r-5" role="status" aria-hidden="true"></span> Loading...');
			$(".employee_modal_submit").attr("disabled", true);
			var dataString = new FormData(form);
			dataString.append('method', 'employee');
			dataString.append('actionType', 'employeeAddEdit');
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
					$('.employee_modal_submit').html('Submit');
					$(".employee_modal_submit").attr("disabled", false);
				
				  if(responseData.RESULT==1)
				  {
					$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
				  }
				  else if(responseData.RESULT==0)
				  {
					$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'success',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
					$('#modal_employee_addedit').modal('hide');
					var oTable = $('#table_employee').dataTable( );
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


$(document).on("click",".employee_delete_onclick", function ()
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
						  data: "method=employee&actionType=employeeDelete&getid="+getid,
						  success: function(responseData)
						  {
							if(responseData.RESULT==0)
							{
							var oTable = $('#table_employee').dataTable( );
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
		swal({ title: "Try Again...",
                text: "Oops Something gone wrong...",
                type: "warning",
				 timer: 1500
            });
			return false;
	}
});


$(document).on("click",".employee_lms_sync", function ()
{
	swal({
		title: "Are you sure?",
		text: "You will not be able to undo after this action!",
		type: "warning",
		showCancelButton: true,
		cancelButtonClass: 'btn-primary',
		confirmButtonClass: 'btn-warning',
		confirmButtonText: "Yes, Sync it!",
		confirmButtonClass: "confirm btn btn-lg btn-warning xyz",
		closeOnConfirm: true
	}, function (r)
	{
		if(r == true)
		{
			$('#custom_ajax_preloader').show();
			$.ajax(
			{
				type: "POST",
				dataType: 'json',
				url: "scripts/ajax/index.php",
				data: "method=employee_lms_sync",
				success: function(responseData)
				{
					$('#custom_ajax_preloader').hide();
					if(responseData.RESULT==1)
					{
						var oTable = $('#table_employee').dataTable( );
						oTable.api().ajax.reload(null, false);
						$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'warning',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
						return false;
					}
					else
					{
						swal({ title: "Try Again...",text: data.msg,type: "warning",timer: 1000});
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
});