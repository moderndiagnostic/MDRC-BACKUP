if ($("#table_journey").length > 0) {
$(document).ready(function(){
	$('#table_journey').DataTable({
		"order": [[ 0, "desc" ]],
		"columnDefs": [ { 'targets': [],"orderable": false } ],
  		'autoWidth':false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'ajax': {
			'url':'scripts/ajax/index.php?method=journey&actionType=journey_list',
			"data": function(d) {
				d.start_date = $('#start_date').val();
				d.end_date = $('#end_date').val();
				d.tab_filter = $('#tab_filter').val();
				d.actionType = "journey_list";     
			  },
		},
		'columns': [
		  	{ "data": "id" },
			{ "data": "employee_name" },
			{ "data": "start_datetime" },
			{ "data": "end_datetime" },
			{ "data": "total_km" },
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
	var oTable = $('#table_journey').dataTable();
	oTable.api().ajax.reload(null, false);
}


$(document).on("click",".export", function ()
{
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();

	if(start_date=='' || end_date==''){
		$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>Select Date First.</p>', {type:'warning',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
		return false;
	}
	else
	{
		var link="index.php?view=journey_list&act=downloadJourney&start_date="+start_date+"&end_date="+end_date;
		$(this).attr('href', link);
		$(this).trigger( "click" );
		return false;
	}
});