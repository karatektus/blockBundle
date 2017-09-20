var plApprovable = function () {
    "use strict";

    return {

        /**
         * Init
         * @param selector
         */
        init: function (selector) {
            this.load($('.approvable', selector));
        },
        /**
         * Load
         * @param id
         */
        load: function (id) {
            var _self = this;
            var links = $(id);

            links.each(function (entry) {
                var link = $(this);
                var no = 'Cancel';
                var yes = 'OK';

                var noClass = 'btn-danger';
                var yesClass = 'btn-success';

                link.css('pointer-events', 'initial');
                link.css('cursor', 'initial');

                if (typeof link.data('yes') !== 'undefined') {
                    yes = link.data('yes');
                }
                if (typeof link.data('no') !== 'undefined') {
                    no = link.data('no');
                }
                if (typeof link.data('yes-class') !== 'undefined') {
                    yesClass = link.data('yes-class');
                }
                if (typeof link.data('no-class') !== 'undefined') {
                    noClass = link.data('no-class');
                }
                link.on("click", function (e) {
                    e.preventDefault();
                    console.log('default prevented');
                    bootbox.confirm({
                        message: link.data('text'),
                        buttons: {
                            confirm: {
                                label: yes,
                                className: yesClass
                            },
                            cancel: {
                                label: no,
                                className: noClass
                            }
                        },
                        callback: function (result) {
                            if (true === result) {
                                window.location.href = link.attr('href');
                            }
                        }
                    });
                });
            });

        }
    };
}();

$(document).ready(function () {
    plApprovable.init();
});