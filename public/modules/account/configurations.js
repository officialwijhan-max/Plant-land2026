
(function($){
    "use strict";
    $(document).ready(function () {
        $(document).on('change', '.in_direct_expense_leadger', function(){
            var indirect_expense = $('.in_direct_expense_leadger').find(':selected').val();
            var direct_expense = $('.direct_expense_leadger').find(':selected').val();
            if (parseInt(indirect_expense) == parseInt(direct_expense)) {
                toastr.warning('Indirect and Direct expense leadger must be different!');
                $('.in_direct_expense_leadger').empty();
                $('.in_direct_expense_leadger').append(' <option value="0">Select One</option>');
                $('.in_direct_expense_leadger').niceSelect('update');
            }
        });
        $(document).on('change', '.direct_expense_leadger', function(){
            var indirect_expense = $('.in_direct_expense_leadger').find(':selected').val();
            var direct_expense = $('.direct_expense_leadger').find(':selected').val();
            if (parseInt(indirect_expense) == parseInt(direct_expense)) {
                toastr.warning('Indirect and Direct expense leadger must be different!');
                $('.direct_expense_leadger').empty();
                $('.direct_expense_leadger').append(' <option value="0">Select One</option>');
                $('.direct_expense_leadger').niceSelect('update');
            }
        });
        $(document).on('change', '.in_direct_income_leadger', function(){
            var indirect_income = $('.in_direct_income_leadger').find(':selected').val();
            var direct_income = $('.direct_income_leadger').find(':selected').val();
            if (parseInt(indirect_income) == parseInt(direct_income)) {
                toastr.warning('Indirect and Direct income leadger must be different!');
                $('.in_direct_income_leadger').empty();
                $('.in_direct_income_leadger').append(' <option value="0">Select One</option>');
                $('.in_direct_income_leadger').niceSelect('update');
            }
        });
        $(document).on('change', '.direct_income_leadger', function(){
            var indirect_income = $('.in_direct_income_leadger').find(':selected').val();
            var direct_income = $('.direct_income_leadger').find(':selected').val();
            if (parseInt(indirect_income) == parseInt(direct_income)) {
                toastr.warning('Indirect and Direct income leadger must be different!');
                $('.direct_income_leadger').empty();
                $('.direct_income_leadger').append(' <option value="0">Select One</option>');
                $('.direct_income_leadger').niceSelect('update');
            }
        });
    });
})(jQuery);
