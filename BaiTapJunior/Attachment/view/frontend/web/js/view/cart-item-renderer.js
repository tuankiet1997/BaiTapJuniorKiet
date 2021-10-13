define([
    'Magento_Checkout/js/view/cart-item-renderer',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        increaseQty: function (itemId){
            var qtyId = '#cart-item-'+itemId+'-qty';
            var updateCartId = '#update-cart-item-'+itemId;
            var currentQty = $(qtyId).val();
            $(qtyId).val(parseInt(currentQty) + 1);
            $(updateCartId).css('display','inline-block');
        },
        decreaseQty: function (itemId){
            var qtyId = '#cart-item-'+itemId+'-qty';
            var updateCartId = '#update-cart-item-'+itemId;
            var currentQty = $(qtyId).val();
            $(qtyId).val(parseInt(currentQty) - 1);
            $(updateCartId).css('display','inline-block');
        }
    });
});
