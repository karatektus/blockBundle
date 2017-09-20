var plsort = function () {
    var alltables = $("#entityBlockTable>tbody");
    alltables.each(function () {
        var tablebody = $(this);
        tablebody.sortable({
            update: function (event, ui) {
                var overlay = $(tablebody).parent().next();
                overlay.css({"display": "block"});
                overlay.removeClass('load-complete');
                $('.checkmark', overlay).hide();
                var $i = tablebody.children().length;
                var data = {};
                tablebody.children().each(function () {
                    $(this).children().first().attr('data-orderId', $i);
                    data[$i] = $(this).children().first().data('slug');
                    $i--;
                });

                $.post({
                    'url': tablebody.parent().data("url"),
                    'data': {'orderData': data},
                    'success': function (response) {
                        console.log(response);
                        $('.circle-loader', overlay).addClass('load-complete');
                        $('.checkmark', overlay).show();
                        setTimeout(function () {
                            overlay.fadeOut("slow");
                        }, 500)
                    }
                });
            }
        });
    });
};
$(document).ready(function () {
    plsort();
});