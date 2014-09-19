$(function() {
	$('#due_date').datepicker({dateFormat: 'dd/mm/yy'});

	$('#add-item').click(function(){
		$('.input-row:first').clone().insertAfter('.input-row:last');
	})

 });