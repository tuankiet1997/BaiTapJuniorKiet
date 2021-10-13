<?php


namespace Magenest\Attachment\Controller\Adminhtml\Attachment;


class Delete extends \Magento\Backend\App\Action
{
    protected $attachmentResource;
    protected $attachment;
    protected $logger;
    protected $cache;

    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $cache,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Attachment\Model\ResourceModel\Attachment $attachmentResource,
        \Magenest\Attachment\Model\AttachmentFactory $attachment,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->attachmentResource = $attachmentResource;
        $this->attachment = $attachment;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function execute()
    {
        try{
            /** @var \Magenest\Attachment\Model\Attachment $attachment */
            $attachment = $this->attachment->create();
            if ($this->_request->getParam('id') ?? false){
                $this->attachmentResource->load($attachment,$this->_request->getParam('id'));
                $this->attachmentResource->delete($attachment);
            }
            $this->cache->invalidate('full_page');
            $this->messageManager->addSuccess(__('This record has been deleted.'));
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->logger->critical($exception->getMessage());
        }
        $this->_redirect('*/*/index');
    }
}