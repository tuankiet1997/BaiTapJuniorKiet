<?php


namespace Magenest\Attachment\Controller\Adminhtml\Attachment;


/**
 * Class Save
 * @package Magenest\Attachment\Controller\Adminhtml\Attachment
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magenest\Attachment\Model\AttachmentFactory
     */
    protected $attachment;
    /**
     * @var \Magenest\Attachment\Model\ResourceModel\Attachment
     */
    protected $attachmentResource;

    /**
     * Save constructor.
     * @param \Magenest\Attachment\Model\ResourceModel\Attachment $attachmentResource
     * @param \Magenest\Attachment\Model\AttachmentFactory $attachment
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\Attachment\Model\ResourceModel\Attachment $attachmentResource,
        \Magenest\Attachment\Model\AttachmentFactory $attachment,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->attachment = $attachment;
        $this->attachmentResource = $attachmentResource;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->_request->getParams();
        try {
            /** @var \Magenest\Attachment\Model\Attachment $attachment */
            $attachment = $this->attachment->create();
            $this->attachmentResource->load($attachment,$this->_request->getParam('id'));
            $attachment->setVisible($params['visible'] ?? 0);
            $attachment->setIncludeOrder($params['include_order'] ?? 0);
            $attachment->setFileName($params['container_group']['file_name'] ?? '');
            $attachment->setFileExtension($params['container_group']['file_extension'] ?? '');
            $attachment->setFileLabel($params['file_label'] ?? '');
            $attachment->setCustomerGroupIds(json_encode($params['customer_group_ids'] ?? []));
            $attachment->setFileSize($params['file_upload'][0]['size'] ?? 0);
            $attachment->setMineType($params['file_upload'][0]['type'] ?? '');
            $attachment->setFileDetail(json_encode($params['file_upload'][0] ?? []));
            $this->attachmentResource->save($attachment);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($redirectBack === 'edit') {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $attachment->getId(), 'back' => null, '_current' => true]
            );
        } else {
            $resultRedirect->setPath('*/*/index');
        }
        return $resultRedirect;
    }
}
