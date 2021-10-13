/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'ko',
    'underscore',
    'Magento_Ui/js/grid/tree-massactions',
    'jquery',
    'Magento_Ui/js/modal/alert',
], function (ko, _, Massactions, $, alert) {
    'use strict';

    return Massactions.extend({
        defaults: {
            submenuTemplate: 'Magenest_ProductListing/ui/grid/menu',
            ids: ko.observable(),
        },
        isInputType: function (inputType) {
            if (inputType == 'related.input') {
                return true;
            }
            return false;
        },

        applyAction: function (actionIndex) {
            var action = this.getAction(actionIndex),
                visibility;
            if (this.isInputType(actionIndex)) {
                if (this.ids() == '' || typeof this.ids() === 'undefined') {
                    alert({
                        content: 'You must type input!'
                    });
                    return this;
                }
                action.url += 'ids/'+this.ids()+'/';
            }
            if (action.visible) {
                visibility = action.visible();

                this.hideSubmenus(action.parent);
                action.visible(!visibility);

                return this;
            }

            return this._super(actionIndex);
        },
    });
});
