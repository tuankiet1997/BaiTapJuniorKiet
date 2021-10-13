<?php
namespace Magenest\Attachment\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;

class Index extends Action
{
    protected $pageResult;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageResult,
        \Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
        $this->pageResult = $pageResult;
    }

    public function execute()
    {
        $pageResult = $this->pageResult->create();
        $pageResult->getConfig()->getTitle()->prepend(__('Attachment'));
        return $pageResult;
    }
}