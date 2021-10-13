<?php


namespace Magenest\Attachment\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\Backend\JsonEncoded;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateCourseDocumentProductAttribute
 * @package Magenest\Attachment\Setup\Patch\Data
 */
class UpdateCourseDocumentProductAttribute implements DataPatchInterface
{
    /**
     *
     */
    const COURSE_DOCUMENT = "course_document";

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * CourseTimelineProductAttribute constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return \Magenest\Attachment\Setup\Patch\Data\UpdateCourseDocumentProductAttribute|void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $eavSetup->updateAttribute(Product::ENTITY, CourseDocumentProductAttribute::COURSE_DOCUMENT, 'is_used_in_grid', true);
        $eavSetup->updateAttribute(Product::ENTITY, CourseDocumentProductAttribute::COURSE_DOCUMENT, 'is_visible_in_grid', true);
        $eavSetup->updateAttribute(Product::ENTITY, CourseDocumentProductAttribute::COURSE_DOCUMENT, 'is_filterable_in_grid', true);
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
