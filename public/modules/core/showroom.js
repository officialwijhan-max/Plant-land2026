
(function($){
    "use strict";
    let _token = $('meta[name=_token]').attr('content');
    $(document).ready(function () {
        getShorrom();

        function getShorrom(){
            $(".showroom_id").select2({
                ajax: {
                    url: $('#showroom_list_select_option').val(),
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1,
                                company: 0
                            }
                            return query;
                    },
                    cache: false
                }
            });
        }
    });
    $(document).on('change', '.showroom_id', function () {
        var value = $(this).find('option:selected').val();
        if (value == "create_new") {
            $('#new_showroom').modal('show');
            var text = $(this).find('option:selected').text().split('->');
            $('.showroom_warehouse_name').val(text[1].replace('</span>',''));

        }
    });

    $("#showroomForm").on("submit", function(event) {
        $('#pre-loader').removeClass('d-none');
        event.preventDefault();
        let formData = $(this).serializeArray();
        var url = base_url('core/location/showroom/instant-normal-store');
        $.ajax({
            url: url,
            data: formData,
            type: "POST",
            success: function(response) {
                $("#new_showroom").modal("hide");
                $("#showroomForm").trigger("reset");
                if (response.option_value != "") {
                    $('.showroom_id').append($('<option>', {value: response.option_value,text: response.option}));
                    $('.showroom_id').val(response.option_value);
                    $('.showroom_id').trigger("change");
                }
                toastr.success(response.success)
                $('#pre-loader').addClass('d-none');
            },
            error: function(error) {
                toastr.error(response.error)
                $('#pre-loader').addClass('d-none');
            }

        });
    });

})(jQuery);
