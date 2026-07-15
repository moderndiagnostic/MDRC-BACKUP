if ($("#table_task_office").length > 0) {
$(document).ready(function(){
	$('#table_task_office').DataTable({
		"order": [[ 0, "desc" ]],
		"columnDefs": [ { 'targets': [4,5],"orderable": false } ],
  		'autoWidth':false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'ajax': {
			"url":'scripts/ajax/index.php?method=task_office&actionType=task_office_list',
			"data": function(d) {
				d.start_date = $('#start_date').val();
				d.end_date = $('#end_date').val();
				d.tab_filter = $('#tab_filter').val();
				d.actionType = "task_office_list";     
			  },
		},
		'columns': [
		 	
		  	{ "data": "id" },
            { "data": "name" },
			{ "data": "time" },
			{ "data": "remark" },
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
	var oTable = $('#table_task_office').dataTable();
	oTable.api().ajax.reload(null, false);
}

$(document).on("click",".export", function ()
{
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();

	var link="index.php?view=task_office_list&act=downloadOfficeTask&start_date="+start_date+"&end_date="+end_date;
	$(this).attr('href', link);
	$(this).trigger( "click" );
	return false;
});