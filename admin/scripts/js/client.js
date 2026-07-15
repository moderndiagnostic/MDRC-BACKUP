if ($("#table_client").length > 0) {
$(document).ready(function(){
	$('#table_client').DataTable({
		"order": [[ 0, "desc" ]],
		"columnDefs": [ { 'targets': [4,5,6],"orderable": false } ],
  		'autoWidth':false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'ajax': {
			"url":'scripts/ajax/index.php?method=client&actionType=client_list',
			"data": function(d) {
				d.start_date = $('#start_date').val();
				d.end_date = $('#end_date').val();
				d.tab_filter = $('#tab_filter').val();
				d.actionType = "client_list";     
			  },
		},
		'columns': [
		 	
		  	{ "data": "id" },
            { "data": "company_name" },
			{ "data": "email" },
			{ "data": "phone" },
			{ "data": "status" },
			{ "data": "employee" },
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
	var oTable = $('#table_client').dataTable();
	oTable.api().ajax.reload(null, false);
}


$(document).on("click",".client_modal_submit", function ()
{
    $('#client_form').validate({
		submitHandler: function (form)
		{
			$('.client_modal_submit').html('<span class="spinner-border spinner-border-sm mg-r-5" role="status" aria-hidden="true"></span> Loading...');
			$(".client_modal_submit").attr("disabled", true);
			var dataString = new FormData(form);
			dataString.append('method', 'client');
			dataString.append('actionType', 'clientAddEdit');
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
					$('.client_modal_submit').html('Submit');
					$(".client_modal_submit").attr("disabled", false);
				
				  if(responseData.RESULT==1)
				  {
					$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
				  }
				  else if(responseData.RESULT==0)
				  {
					$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'success',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
					/* $('#modal_client_addedit').modal('hide');
					var oTable = $('#table_client').dataTable( );
					oTable.api().ajax.reload(null, false); */
					window.location.href = 'index.php?view=client_list';
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


$(document).on("click",".client_delete_onclick", function ()
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
					$.ajax(
					{
						  type: "POST",
						  dataType: 'json',
						  url: "scripts/ajax/index.php",
						  data: "method=client&actionType=clientDelete&getid="+getid,
						  success: function(responseData)
						  {
								  if(responseData.RESULT==0)
								  {
									var oTable = $('#table_client').dataTable( );
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
						  }
					  );
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


function mulitple_client_select()
{
			var chk_vals=[];
	  	    $('input[name="del[]"]:checked').each(function() {chk_vals.push($(this).val());});
			if(chk_vals.length>0)
			{
				var ids=chk_vals.join(',');
				swal({
					title: "Are you sure?",
					text: "you want to delete records?",
					type: "warning",
					showCancelButton: true,
					cancelButtonClass: 'btn-primary',
					confirmButtonClass: 'btn-warning',
					confirmButtonText: "Yes, delete it!",
					confirmButtonClass: "confirm btn btn-lg btn-warning xyz",
					closeOnConfirm: true
					},
					function (r){
						if(r == true)
						  {
							  $.ajax({
							  type: "POST",
							  dataType: 'json',
							  url: "scripts/ajax/index.php",
							  data: "method=client&actionType=clientMultiDelete&ids="+ids,
							  success: function(responseData){
								  if(responseData.RESULT==0)
								  {
									  $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'success',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20}});
								  }
								  else
								  {
									  $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20}});
								  }
								  var oTable = $('#table_client').dataTable( );
								  oTable.api().ajax.reload(null, false);
							  }
						  });
						 }
						else
						{
							return false;
						}
					}
				);
			}
			else
			{
				swal({
						 title:"Please Select Record",
						 type:"warning",
              			 timer: 1500
           			 });
			return false;
			}
}


$(document).on("click",".client_lms_sync", function ()
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
				data: "method=client_lms_sync",
				success: function(responseData)
				{
					$('#custom_ajax_preloader').hide();
					if(responseData.RESULT==1)
					{
						var oTable = $('#table_client').dataTable( );
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


$(document).on("click",".client_lms_sync_logistic", function ()
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
				data: "method=client_lms_sync_logistic",
				success: function(responseData)
				{
					$('#custom_ajax_preloader').hide();
					if(responseData.RESULT==1)
					{
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

