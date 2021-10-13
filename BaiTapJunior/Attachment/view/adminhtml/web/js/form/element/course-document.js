define([
    'jquery',
    'ko',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function ($, ko, uiRegistry, input) {
    'use strict';

    return input.extend({
        defaults: {
            listFile: ko.observable([]),
            isHaveRecommend: ko.observable(false)
        },
        initialize: function () {
            var self = this;
            this._super();
            this.isHaveRecommend = ko.computed(function () {
                if (self.listFile().length) {
                    return 1;
                }
                return 0;
            });
            return this;
        },
        loadRecommend: function () {
            var value = $('input[name="product[course_document]"]').val();
            var self = this;
            if (value.length >= 3) {
                $.ajax({
                    type: 'GET',
                    data: {
                        query: value,
                    },
                    url: this.searchUrl,
                    showLoader: false,
                    success: function (response) {
                        self.listFile(response);
                    },
                    error: function () {
                    }
                });
            } else {
                self.listFile([]);
            }
            return true;
        },
        updateDocumentField: function (value) {
            if (uiRegistry.get('index = course_document')) {
                uiRegistry.get('index = course_document').value(value);
            }
        }
    });
});
