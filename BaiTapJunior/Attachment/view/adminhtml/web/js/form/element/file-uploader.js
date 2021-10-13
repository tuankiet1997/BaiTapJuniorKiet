define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/file-uploader',
], function ($,uiRegistry, uploader) {
    'use strict';

    return uploader.extend({
        onFileUploaded: function (event, data) {
            this._super();
            this.updateFileIcon();
            var name = data.files[0].name.split('.')[0];
            var extension = data.files[0].name.split('.')[1];
            if (uiRegistry.get('index = file_name')) {
                uiRegistry.get('index = file_name').value(name);
            }
            if (uiRegistry.get('index = file_extension')) {
                uiRegistry.get('index = file_extension').value(extension);
            }
            if (uiRegistry.get('index = file_label')) {
                uiRegistry.get('index = file_label').value(name);
            }
        },
        updateFileIcon: function () {
            var fileUpload = uiRegistry.get('index = file_upload');
            var iconSelector = $('.file-uploader-preview');
            $('a').attr('background-image','http://m243.local.com/static/version1630115096/adminhtml/Magento/backend/en_US/images/magento-icon.svg');
        },
        getFilePreview: function (file) {
            return file.icon_extension;
        },
        getLinkPreview: function (file) {
            return file.link_file;
        },
    });
});
