<?php


namespace Magenest\OrderCancel\Controller\Adminhtml\Cancellation;


class Index extends \Magento\Backend\App\Action
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
        $pageResult->getConfig()->getTitle()->prepend(__('Cancellation Request'));
        return $pageResult;
    }
}
