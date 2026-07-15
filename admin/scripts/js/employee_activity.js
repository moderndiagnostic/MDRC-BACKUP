// ==========================LOAD-DATA-TABLE===================================

if ($("#table_employee_activity").length > 0) {
	$(document).ready(function(){
		$('#table_employee_activity').DataTable({
			"order": [[ 1, "desc" ]],
			"columnDefs": [ { 'targets': [0],"orderable": false } ],
			  'autoWidth':false,
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				'url':'scripts/ajax/index.php?method=employee_activity&actionType=employee_activity_list',
				"data": function(d) {
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.tab_filter = $('#tab_filter').val();
					d.package = $('#package').val();
					d.actionType = "employee_activity_list";     
				  },
			},
			'columns': [
				{ "data": "checkbox" },
				{ "data": "id" },
				{ "data": "employee_name" },
				{ "data": "title" },
				{ "data": "ip" },
				{ "data": "updated_at" },
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
		$('[data-toggle="tooltip"]').tooltip();
	});
	}
	
	function search_data() {
		load_datatable();
	}
	
	function reset_data() {
		$("#start_date").val('');
		$("#end_date").val('');
		$("#package").val('');
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
		var oTable = $('#table_employee_activity').dataTable();
		oTable.api().ajax.reload(null, false);
	}
	

// ==========================SINGLE-DELETE==========================

	$(document).on("click",".employee_activity_delete_onclick", function ()
	{
		var ids=$(this).data('id');
		if(ids!='')
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
							  data: "method=employee_activity&actionType=employee_activityMultiDelete&ids="+ids,
							  success: function(responseData)
							  {
									  if(responseData.RESULT==0)
									  {
										var oTable = $('#table_employee_activity').dataTable( );
										oTable.api().ajax.reload(null, false);
										$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>Record Deleted Successfully.</p>', {type:'warning',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
										 return false;
									  }
									  else if(responseData.RESULT==1)
									  {
										  $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
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

// ===============================MULITPLE-DELETE=====================================
	
function mulitple_employee_activity_select()
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
							  data: "method=employee_activity&actionType=employee_activityMultiDelete&ids="+ids,
							  success: function(responseData){
								  if(responseData.RESULT==0)
								  {
									  $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'success',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20}});
								  }
								  else
								  {
									  $.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20}});
								  }
								  var oTable = $('#table_employee_activity').dataTable( );
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

	function getRefMobileField(value){
	
		if(value=="Ref. employee_activity or Lead")
		{
			$(".getRefMobileField").show();
			$(".getRefMobileField").removeClass("d-none");
		}
		else
		{
			$(".getRefMobileField").hide();
		}
	}
	
	