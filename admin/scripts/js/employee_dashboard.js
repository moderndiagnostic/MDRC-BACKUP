$(document).ready(function() {
    getData();
})
function getData(){
	var dataString = new FormData($('#frm_search')[0]);
	dataString.append('method', 'employee_dashboard');
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
			}
		},
		error: function (responseData) {
			console.log('Ajax request not received!');
		}
	});
}
function getDataSub(){
	
	var dataString = new FormData($('#frm_search')[0]);
	dataString.append('method', 'employee_dashboard');
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

					$('.selectedEmployeeTeamCard').show();
					$('.taskHtml5').html(responseData.html5);
					$('.selectedEmployeeTeamName').html(responseData.selected_employee_name+' Team Tasks');
					setChart4(responseData.chartData4);
				}
				else
				{
					$('.selectedEmployeeCard').hide();
					$('.selectedEmployeeTeamCard').hide();

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
	$('.selectedEmployeeTeamCard').hide()
}
// var myDoughnutChart=null;
// function setChart(chartData) {
//     // Define chart data
//     var datapie = {
//         labels: chartData.chartLabel || [],
//         datasets: [{
//             data: chartData.chartValue || [],
//             backgroundColor: chartData.chartColor || []
//         }]
//     };

//     // Define chart options
//     var optionpie = {
//         maintainAspectRatio: false,
//         responsive: true,
//         plugins: {
//             legend: {
//                 display: false
//             }
//         },
//         animation: {
//             animateScale: true,
//             animateRotate: true
//         }
//     };
// 	if (myDoughnutChart) {
//         myDoughnutChart.destroy(); // 🧹 fully clear old chart
//     }
//     // Initialize the new chart instance
//     var ctx1 = document.getElementById('chartDonut').getContext('2d');
//     myDoughnutChart = new Chart(ctx1, {
//         type: 'doughnut',
//         data: datapie,
//         options: optionpie
//     });
// }
// var myDoughnutChart1=null;
// function setChart2(chartData) {
//     // Define chart data
//     var datapie = {
//         labels: chartData.chartLabel || [],
//         datasets: [{
//             data: chartData.chartValue || [],
//             backgroundColor: chartData.chartColor || []
//         }]
//     };

//     // Define chart options
//     var optionpie = {
//         maintainAspectRatio: false,
//         responsive: true,
//         plugins: {
//             legend: {
//                 display: false
//             }
//         },
//         animation: {
//             animateScale: true,
//             animateRotate: true
//         }
//     };

//     // Initialize the new chart instance
//     var ctx2 = document.getElementById('chartDonut2').getContext('2d');
//     myDoughnutChart1 = new Chart(ctx2, {
//         type: 'doughnut',
//         data: datapie,
//         options: optionpie
//     });
// }
// var myDoughnutChart2=null;
// function setChart3(chartData) {
//     // Define chart data
//     var datapie = {
//         labels: chartData.chartLabel || [],
//         datasets: [{
//             data: chartData.chartValue || [],
//             backgroundColor: chartData.chartColor || []
//         }]
//     };

//     // Define chart options
//     var optionpie = {
//         maintainAspectRatio: false,
//         responsive: true,
//         plugins: {
//             legend: {
//                 display: false
//             }
//         },
//         animation: {
//             animateScale: true,
//             animateRotate: true
//         }
//     };

//     // Initialize the new chart instance
//     var ctx3 = document.getElementById('chartDonut3').getContext('2d');
//     myDoughnutChart2 = new Chart(ctx3, {
//         type: 'doughnut',
//         data: datapie,
//         options: optionpie
//     });
// }


var myDoughnutChart = null;
function setChart(chartData) {
    const datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || [],
        }]
    };

    const optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Destroy previous instance if exists
    if (myDoughnutChart) {
        myDoughnutChart.destroy();
    }

    const ctx1 = document.getElementById('chartDonut').getContext('2d');
    myDoughnutChart = new Chart(ctx1, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });
}

var myDoughnutChart1 = null;
function setChart2(chartData) {
    const datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || [],
        }]
    };

    const optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Destroy previous instance
    if (myDoughnutChart1) {
        myDoughnutChart1.destroy();
    }

    const ctx2 = document.getElementById('chartDonut2').getContext('2d');
    myDoughnutChart1 = new Chart(ctx2, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });
}

var myDoughnutChart2 = null;
function setChart3(chartData) {
    const datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || [],
        }]
    };

    const optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Destroy previous instance
    if (myDoughnutChart2) {
        myDoughnutChart2.destroy();
    }

    const ctx3 = document.getElementById('chartDonut3').getContext('2d');
    myDoughnutChart2 = new Chart(ctx3, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });
}

var myDoughnutChart3 = null;
function setChart4(chartData) {
    const datapie = {
        labels: chartData.chartLabel || [],
        datasets: [{
            data: chartData.chartValue || [],
            backgroundColor: chartData.chartColor || [],
        }]
    };

    const optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    // Destroy previous instance
    if (myDoughnutChart3) {
        myDoughnutChart3.destroy();
    }

    const ctx3 = document.getElementById('chartDonut4').getContext('2d');
    myDoughnutChart3 = new Chart(ctx3, {
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
			url: 'scripts/modal/index.php?method=view_task&purpose='+purpose+'&id='+getId+'&name='+name,
			dataType : 'html',
			data: $(this).serialize()
		})
		.done(function(data)
		{
			// show the response
			$('#ajax_modal_container').html(data);
			$('#modal_view_task').modal('show');
			$('#custom_ajax_preloader').hide();
			$('#table_employee_task').DataTable({
				"order": [[ 0, "desc" ]],
				"columnDefs": [ { 'targets': [0],"orderable": false } ],
				'autoWidth':false,
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'url':'scripts/ajax/index.php?method=employee_dashboard&actionType=employee_task_list',
					"data": function(d) {
						d.id = $('#id').val();
						d.purpose = $('#purpose').val();
						d.start_date = $('#start_date').val();
						d.end_date = $('#end_date').val();
					},
				},
				'columns': [
					{ "data": "id" },
					{ "data": "client" },
					{ "data": "employee" },
					{ "data": "purpose" },
					{ "data": "created_at" },
					{ "data": "image" }
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

	