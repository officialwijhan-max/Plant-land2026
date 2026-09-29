
(function($){
    "use strict";
    $(document).ready(function () {
        var dtbl_url = $('#dtbl_url').val();
        let _token = $('meta[name=_token]').attr('content') ;
        $(document).on('click','.voucher_detail', function(){
            $('.voucher_detail').addClass('d-none');
            $('#pre-loader').removeClass('d-none');
            let url = $('#details_url').val();
            var id = $(this).attr("data-id");
            url = url.replace(':id',id);
            console.log(url);
            $.ajax({
                url: url,
                type: "GET",
                dataType: "HTML",
                success: function (response) {
                    $('#Voucher_info').html(response);
                    $('#Voucher_info_modal').modal('show');
                    $('select').niceSelect();
                    $('#pre-loader').addClass('d-none');
                    $('.voucher_detail').removeClass('d-none');
                },
                error: function (error) {
                    $('#pre-loader').addClass('d-none');
                    $('.voucher_detail').removeClass('d-none');
                }
            });
        });
    });

})(jQuery);
