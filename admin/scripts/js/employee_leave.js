if ($("#table_employee_leave").length > 0) {
	$(document).ready(function(){
		$('#table_employee_leave').DataTable({
			"order": [[ 0, "desc" ]],
			"columnDefs": [ { 'targets': [5],"orderable": false } ],
			  'autoWidth':false,
			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			'ajax': {
				"url":'scripts/ajax/index.php?method=employee_leave&actionType=employee_leave_list',
				"data": function(d) {
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.tab_filter = $('#tab_filter').val();
					d.actionType = "employee_leave_list";     
				  },
			},
			'columns': [
				 
				  { "data": "id" },
				{ "data": "employee_name" },
				{ "data": "leave_start" },
				{ "data": "leave_end" },
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
		var oTable = $('#table_employee_leave').dataTable();
		oTable.api().ajax.reload(null, false);
	}

// OPEN-DETAIL-PAGE

$(document).on("click",".employee_leave_detail_onclick", function ()
{
	getId=$(this).data("id");
	$('#custom_ajax_preloader').show();
	$.ajax({
	type: 'POST',
	url: 'scripts/modal/index.php?method=employee_leave_detail&id='+getId,
	dataType : 'html',
	data: $(this).serialize()
	})
	.done(function(data)
	{
		// show the response
		$('#ajax_modal_container').html(data);
		$('#modal_employee_leave_detail').modal('show');
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