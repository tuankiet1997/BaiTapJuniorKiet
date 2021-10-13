<?php


namespace Magenest\Attachment\Plugin\Block\Product;


/**
 * Class AbstractProduct
 * @package Magenest\Attachment\Plugin\Block\Product
 */
class AbstractProduct
{
    /**
     * @var \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollection;

    /**
     * AbstractProduct constructor.
     * @param \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection
     */
    public function __construct(
        \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection
    ) {
        $this->attachmentCollection = $attachmentCollection;
    }

    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param callable $next
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        callable $next,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $next($product);
        $documentName = $product->getCourseDocument();
        $attachment = $this->attachmentCollection->create()
            ->addFieldToFilter('file_name',['eq' => $documentName])
            ->getFirstItem();
        $fileDetail = json_decode($attachment->getFileDetail(),true);
        if (isset($fileDetail['link_file']) && $attachment->getFileLabel()) {
            $attachmentHtml =   '<div class="magenest-attachment" style="margin-bottom: 15px;">
                                <a href="'.$fileDetail['link_file'].'">'.$attachment->getFileLabel().'</a>
                            </div>';
        } else {
            $attachmentHtml = '';
        }
        return $result.$attachmentHtml;
    }
}
