<?php


namespace Magenest\Attachment\Helper;


use Magento\Framework\App\Helper\AbstractHelper;

class DataHelper extends AbstractHelper
{
    protected $assetRepository;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->assetRepository = $assetRepository;
    }

    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->_request->isSecure()], $params);
            return $this->assetRepository->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return $this->_urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }

    public function getIconExtension($extension) {
        switch ($extension) {
            case 'jpg':
            case 'png':
            case 'gif':
            case 'jpeg':
                return $this->getViewFileUrl('Magenest_Attachment::images/Image.png');
            case 'zip':
            case 'rar':
                return $this->getViewFileUrl('Magenest_Attachment::images/Archive.png');
            case 'csv':
            case 'xlsx':
                return $this->getViewFileUrl('Magenest_Attachment::images/Table.png');
            case 'pptx':
                return $this->getViewFileUrl('Magenest_Attachment::images/Presentation.png');
        }
        return $this->getViewFileUrl('Magenest_Attachment::images/Document.png');
    }

    public function formatSize(int $size)
    {
        $sizeFormat = round($size / 1024,2);
        return $sizeFormat.' KB';
    }

}
