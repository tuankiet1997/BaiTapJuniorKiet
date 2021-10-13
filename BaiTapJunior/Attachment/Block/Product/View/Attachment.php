<?php


namespace Magenest\Attachment\Block\Product\View;


/**
 * Class Attachment
 * @package Magenest\Attachment\Block\Product\View
 */
class Attachment extends CourseAbstractView
{
    /**
     * @return \Magento\Framework\DataObject
     */
    public function getAttachment()
    {
        $product = $this->registry->registry('product');
        $documentName = $product->getCourseDocument();
        $attachment = $this->attachmentCollection->create()
            ->addFieldToFilter('file_name',['eq' => $documentName])
            ->getFirstItem();
        return $attachment;
    }
}
