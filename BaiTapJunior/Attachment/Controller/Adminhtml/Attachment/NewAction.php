<?php


namespace Magenest\Attachment\Controller\Adminhtml\Attachment;


class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }
}