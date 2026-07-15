//select 2 reload


$(".modal-content .select2").select2({
    dropdownParent: $('.modal-body')
});

//input with number validation

$('.numbersOnly').keyup(function () {

    if (this.value != this.value.replace(/[^0-9\.]/g, '')) {

       this.value = this.value.replace(/[^0-9\.]/g, '');

    }

});





//datepicker

$('.input-datepicker, .input-daterange').datepicker({weekStart: 1, dateFormat: 'mm/dd/yy'}).on('changeDate', function(e){ $(this).datepicker('hide'); });



//for holiday modal

$('.chosen-toggle-holiday-select').each(function(index) {

    $(this).on('click', function(){

		 $("#holiday-zone-select > option").prop("selected","selected");// Select All Options

		 $('#holiday-zone-select').trigger('change');

    });

});



$('.chosen-toggle-holiday-deselect').each(function(index) {

    $(this).on('click', function(){

		 $('#holiday-zone-select').val(null).trigger('change');

    });

});











