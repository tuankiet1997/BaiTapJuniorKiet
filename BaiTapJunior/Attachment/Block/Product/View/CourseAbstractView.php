<?php


namespace Magenest\Attachment\Block\Product\View;

use Magento\Framework\View\Element\Template;

/**
 * Class CourseAbstractView
 * @package Magenest\Attachment\Block\Product\View
 */
class CourseAbstractView extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollection;

    /**
     * CourseAbstractView constructor.
     * @param \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection,
        \Magento\Framework\Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->attachmentCollection = $attachmentCollection;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        $product = $this->registry->registry('product');
        if ($product->getTypeId() == \Magenest\Attachment\Model\Product\Type\CourseProductType::TYPE_CODE) {
            return true;
        }
        return false;
    }
}
