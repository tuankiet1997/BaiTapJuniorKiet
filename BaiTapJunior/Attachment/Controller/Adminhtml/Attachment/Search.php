<?php

namespace Magenest\Attachment\Controller\Adminhtml\Attachment;


use Magento\Backend\App\Action;

class Search extends Action
{
    protected $attachmentCollection;
    protected $jsonResult;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->attachmentCollection= $attachmentCollection;
        $this->jsonResult = $jsonResult;
    }

    public function execute()
    {
        $result = $this->jsonResult->create();
        $searchQuery = $this->_request->getParam('query');
        $attachmentCollection = $this->attachmentCollection->create()
            ->addFieldToFilter('file_name',['like' => '%'.$searchQuery.'%']);
        $result->setData($attachmentCollection->getColumnValues('file_name'));
        return $result;
    }
}
