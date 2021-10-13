define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'mage/translate'
], function ($, modal, urlBuilder) {
    'use strict';
    return function (config) {
        var order_id = null;
        var options = {
            responsive: true,
            innerScroll: true,
            title: 'Cancel Order',
            buttons: [
                {
                    text: $.mage.__('Cancel'),
                    class: 'magenest-cancel-order-cancel-button',
                    click: function () {
                        this.closeModal();
                    }
                },
                {
                    text: $.mage.__('Submit'),
                    class: 'magenest-cancel-order-submit-button',
                    click: function () {
                        var reason = $('select[name="reason_cancel"]').val();
                        var comment = $('textarea[name="comment_cancel"]').val();
                        var urlQuery = urlBuilder.build('order/cancellation/submit');
                        $.ajax({
                                url: urlQuery,
                                data: {
                                    order_id: order_id,
                                    reason: reason,
                                    comment: comment
                                },
                                type: 'post',
                                showLoader: true
                            }
                        ).done(
                            function (response) {
                                window.location.reload();
                            },
                        ).fail(
                            function (response) {
                                window.location.reload();
                            },
                        );
                    }
                }
            ]
        };

        var popup = modal(options, $('#magenest-cancel-modal'));
        $(".magenest_cancel_order").on('click',function(e){
            e.preventDefault();
            order_id = $(this).attr('order_id');
            $("#magenest-cancel-modal").modal("openModal");
        });

    }
});
