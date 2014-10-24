$(function() {
    
    $('.btn-invoice-paid').click(function() {
        return confirm('Has this invoice been paid ?');
    });

    /**
     * reset the form and show it!
     */
    $('.btn-send-reminder').click(function(e){
        e.preventDefault();
        $('#user-form-data').each(function(){
            this.reset();
        });
        $('#invoice-id').val($(this).data('id'));
        $('#btn-send-reminder').attr('data-method', 'POST');
        $('#reminder-modal').modal('show');
    });

});