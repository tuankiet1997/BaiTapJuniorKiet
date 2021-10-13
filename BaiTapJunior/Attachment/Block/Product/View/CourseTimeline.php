<?php


namespace Magenest\Attachment\Block\Product\View;

/**
 * Class CourseTimeline
 * @package Magenest\Attachment\Block\Product\View
 */
class CourseTimeline extends CourseAbstractView
{
    /**
     * @return mixed
     */
    public function getCourseTimeline() {
        $product = $this->registry->registry('product');
        $courseTimeline = $product->getCourseTimeline();
        return $courseTimeline;
    }
}
