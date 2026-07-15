if ($("#table_employee_team").length > 0) {
    $(document).ready(function(){
        $('#table_employee_team').DataTable({
            "order": [[ 0, "desc" ]],
            "columnDefs": [ { 'targets': [3],"orderable": false } ],
              'autoWidth':false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'scripts/ajax/index.php?method=employee_detail&actionType=employee_team_list',
                "data": function(d) {
                    d.lms_employee_id = $('#lms_employee_id').val();
                    // d.start_date = $('#start_date').val();
                    // d.end_date = $('#end_date').val();
                    // d.tab_filter = $('#tab_filter').val();
                    // d.actionType = "employee_list";     
                  },
            },
            'columns': [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
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

if ($("#table_employee_client").length > 0) {
    $(document).ready(function(){
        $('#table_employee_client').DataTable({
            "order": [[ 0, "desc" ]],
            "columnDefs": [ { 'targets': [1],"orderable": false } ],
            'autoWidth':false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                "url":'scripts/ajax/index.php?method=employee_detail&actionType=employee_client_list',
                "data": function(d) {
                    // d.start_date = $('#start_date').val();
                    // d.end_date = $('#end_date').val();
                    // d.tab_filter = $('#tab_filter').val();
                    d.lms_employee_id = $('#lms_employee_id').val();
                    d.actionType = "client_list";     
                  },
            },
            'columns': [
                 
                { "data": "id" },
                { "data": "company_name" },
                { "data": "email" },
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