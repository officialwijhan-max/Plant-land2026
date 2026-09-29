
(function($){
    "use strict";
    $(document).ready(function () {
        let _token = $('meta[name=_token]').attr('content') ;
        $(".download_excel").on('click',function(){
            if (window.location.href.indexOf('?') > -1) {
                window.open(window.location.href+'&excel',"_blank")
            }else {
                window.open(window.location.href+'?excel',"_blank")
            }
        });
        $(".download_pdf").on('click',function(){
            if (window.location.href.indexOf('?') > -1) {
                window.open(window.location.href+'&print=1',"_blank")
            }else {
                window.open(window.location.href+'?print=1',"_blank")
            }
        });
    });

})(jQuery);
