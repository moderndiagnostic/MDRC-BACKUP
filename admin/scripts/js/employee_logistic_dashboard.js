$(document).ready(function() {
    getData();
})
function getData(){
	var dataString = new FormData($('#frm_search')[0]);
	dataString.append('method', 'employee_logistic_dashboard');
	dataString.append('actionType', 'getData');
	dataString.append('selected_employee', $('.employeeList li a.active').data('id') || '');
	dataString.append('selected_employee_name', $('.employeeList li a.active').data('name') || '');
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
			if(responseData.RESULT==1)
			{
				$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
			}
			else if(responseData.RESULT==0)
			{
				$('.taskHtml1').html(responseData.html1);
				$('.taskHtml2').html(responseData.html2);
				$('.taskHtml3').html(responseData.html3);
				setChart(responseData.chartData1);
				setChart2(responseData.chartData2);
				$('.dataTable').DataTable();
			}
		},
		error: function (responseData) {
			console.log(responseData);
		}
	});
}
function getDataSub(){
	
	var dataString = new FormData($('#frm_search')[0]);
	dataString.append('method', 'employee_logistic_dashboard');
	dataString.append('actionType', 'getDataSub');
	dataString.append('selected_employee', $('.employeeList li a.active').data('id') || '');
	dataString.append('selected_employee_name', $('.employeeList li a.active').data('name') || '');
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
			if(responseData.RESULT==1)
			{
				$.bootstrapGrowl('<h4><strong>Notification</strong></h4> <p>'+responseData.msg+'</p>', {type:'danger',delay: 3000,allow_dismiss: true,offset: {from: 'top', amount: 20} });
			}
			else if(responseData.RESULT==0)
			{
				if(responseData.selected_employee)
				{
					$('.selectedEmployeeCard').show();
					$('.taskHtml4').html(responseData.html4);
					$('.selectedEmployeeName').html(responseData.selected_employee_name+' Tasks');
					setChart3(responseData.chartData3);
					$('.dataTable').DataTable();
				}
				else
				{
					$('.selectedEmployeeCard').hide();
				}
			}
		},
		error: function (responseData) {
			console.log('Ajax request not received!');
		}
	});
	
}
function search_data() {
	getData();
	getDataSub();
}

function reset_data() {
	$("#start_date").val('');
	$("#end_date").val('');
	getData();
	getDataSub();
}

$(document).on("click",".employeeList li a", function ()
{
	$('.employeeList li a').removeClass('active');
	$(this).addClass('active');
	getDataSub();
});
function closeSubEmployee() {
	$('.selectedEmployeeCard').hide()
}

function setChart(chartData) {
    // Define chart data
    var datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || []
        }]
    };

    // Define chart options
    var optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Initialize the new chart instance
    var ctx2 = document.getElementById('chartDonut').getContext('2d');
    var myDoughnutChart = new Chart(ctx2, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });
}

function setChart2(chartData) {
    // Define chart data
    var datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || []
        }]
    };

    // Define chart options
    var optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Initialize the new chart instance
    var ctx2 = document.getElementById('chartDonut2').getContext('2d');
    var myDoughnutChart = new Chart(ctx2, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });
}
function setChart3(chartData) {
    // Define chart data
    var datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || []
        }]
    };

    // Define chart options
    var optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Initialize the new chart instance
    var ctx2 = document.getElementById('chartDonut3').getContext('2d');
    var myDoughnutChart = new Chart(ctx2, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });
}


$(document).on("click",".viewTask", function ()
	{
		var getId=$(this).data("id");
		var purpose=$(this).data("purpose");
		var name=$(this).data("name");
		$('#custom_ajax_preloader').show();
		$.ajax({
			type: 'POST',
			url: 'scripts/modal/index.php?method=view_visits&purpose='+purpose+'&id='+getId+'&name='+name,
			dataType : 'html',
			data: $(this).serialize()
		})
		.done(function(data)
		{
			// show the response
			$('#ajax_modal_container').html(data);
			$('#modal_view_visit').modal('show');
			$('#custom_ajax_preloader').hide();
			$('#table_employee_visit').DataTable({
				"order": [[ 0, "desc" ]],
				"columnDefs": [ { 'targets': [0],"orderable": false } ],
				'autoWidth':false,
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'url':'scripts/ajax/index.php?method=employee_logistic_dashboard&actionType=employee_visit_list',
					"data": function(d) {
						d.id = $('#id').val();
						d.purpose = $('#purpose').val();
						d.start_date = $('#start_date').val();
						d.end_date = $('#end_date').val();
					},
				},
				'columns': [
					{ "data": "id" },
					{ "data": "employee_name" },
					{ "data": "client_company_name" },
					{ "data": "pickup_date" },
					{ "data": "summary" },
				],
				 language:
				 {
					searchPlaceholder: 'Search...',
					sSearch: '',
					lengthMenu: '_MENU_ items/page',
				}
			});
		})
		.fail(function()
		{
			// just in case posting your form failed
			alert( "Try again." );
			$('#custom_ajax_preloader').hide();
		});
	});

	