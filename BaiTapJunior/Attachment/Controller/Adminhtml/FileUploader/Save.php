<?php


namespace Magenest\Attachment\Controller\Adminhtml\FileUploader;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Magenest\Attachment\Controller\Adminhtml\Attachment\FileUploader
 */
class Save extends Action
{
    /**
     * @var \Magento\Framework\File\UploaderFactory
     */
    protected $uploader;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    protected $attachmentHelper;

    public function __construct(
        \Magenest\Attachment\Helper\DataHelper $attachmentHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\App\Action\Context $context
    ){
        parent::__construct($context);
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->attachmentHelper = $attachmentHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $uploader = $this->uploader->create(['fileId' => 'file_upload']);
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath('magenest/attachment');
        try {
            $result = $uploader->save($path);
            if ($result) {
                $result['link_file'] = $this->urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'magenest/attachment/'.$uploader->getUploadedFileName();
//                $result['icon_extension'] = $this->attachmentHelper->getIconExtension($uploader->getFileExtension());
            }
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        };
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData($result);
    }
}
