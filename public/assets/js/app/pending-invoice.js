$(function() {

    //var $loader = $('#loader');

    /**
     * send PUT request to the resouce server
     */
    $('#invoice-table').on('click', '.btn-invoice-paid', function(e){
        var $invoiceid = $(this).attr('data-id');
        e.preventDefault();

        console.log(global.baseUrl+'invoice/'+$invoiceid);

        if(confirm('Has this invoice been paid ?')){
            var request = $.ajax({
                url    : global.baseUrl+'invoice/'+$invoiceid,
                method : 'PUT',
                data   : {
                    'id' : $invoiceid,
                    'paid' : 1
                },
                success : function(resp){
                    if(resp.success){
                        $('#invoice-row-'+$invoiceid).remove();
                        alert(resp.message);
                    }else{
                        alert(resp.message);
                        if(resp.code == 401){
                            location.reload();
                        }
                    }
                }
            });
        }
    });
});