<?php


namespace Magenest\Attachment\Model\Product\Type;


class CourseProductType extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_CODE = 'course_product';

    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {

    }

    public function isVirtual($product)
    {
        return true;
    }
}