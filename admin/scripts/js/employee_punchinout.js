if ($("#table_employee_punchinout").length > 0) {
$(document).ready(function(){
	$('#table_employee_punchinout').DataTable({
		"order": [[ 0, "desc" ]],
		"columnDefs": [ { 'targets': [4,6],"orderable": false } ],
  		'autoWidth':false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'ajax': {
			"url":'scripts/ajax/index.php?method=employee_punchinout&actionType=employee_punchinout_list',
			"data": function(d) {
				d.start_date = $('#start_date').val();
				d.end_date = $('#end_date').val();
				d.tab_filter = $('#tab_filter').val();
				d.actionType = "employee_punchinout_list";     
			  },
		},
		'columns': [
		  	{ "data": "id" },
            { "data": "name" },
			{ "data": "mobile" },
			{ "data": "in" },
			{ "data": "out" },
			{ "data": "image" },
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
	var oTable = $('#table_employee_punchinout').dataTable();
	oTable.api().ajax.reload(null, false);
}