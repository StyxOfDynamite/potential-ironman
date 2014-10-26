$(function() {
	/** DatePicker functionality */
	$('#due_date').datepicker({dateFormat: 'dd/mm/yy'});

	/** calculate item total */
	  $('input[name="item_qty[]"],input[name="item_price[]"]').on("change", function () {
	  	var $container = $(this).closest('.form-group');
	  	qty = Number($('input[name="item_qty[]"]',$container).val())||0,
	  	price = Number($('input[name="item_price[]"]',$container).val())||0;

	  	$('input[name="item_total[]"]',$container).val(qty * price);
	  	$('input[name="item_total[]"]',$container).change();

  });

	/** Add additional item lines */
	$('#add-item').click(function(e){
		e.preventDefault();
		/** clone first line and insert it */
		$('.input-row:first').clone().insertAfter('.input-row:last');
		/** clear the newly inserted inputs of values */
		$(':input', '.input-row:last').val("");
		/** ensure all item price and qty inputs have events attached to their change value */
		$('input[name="item_qty[]"],input[name="item_price[]"]').on("change",function () {
			var $container = $(this).closest('.form-group');
	  		qty = Number($('input[name="item_qty[]"]',$container).val())||0,
	  		price = Number($('input[name="item_price[]"]',$container).val())||0;
	  		$('input[name="item_total[]"]',$container).val(qty * price);
	  		$('input[name="item_total[]"]',$container).change();
	  	});
	  	/** Sum inputs for invoice total */
		$('input[name="item_total[]"').change(function() {
			var total = 0;
			$.each($("[name='item_total[]']"), function(index, value) {
				total += parseFloat($(this).val());
			});
			$("#total").val(total);
		});
	});

	

	/** Sum inputs for invoice total */
	$('input[name="item_total[]"').change(function() {
		var total = 0;
		$.each($("[name='item_total[]']"), function(index, value) {
			total += parseFloat($(this).val());
		});
		$("#total").val(total);
	});

	$("#remove-item").click(function(e) {
		if ($('.input-row').length > 1)  {
			$('.input-row:last').remove();
			$('input[name="item_total[]"').change();
		}
		e.preventDefault();
	});

 });