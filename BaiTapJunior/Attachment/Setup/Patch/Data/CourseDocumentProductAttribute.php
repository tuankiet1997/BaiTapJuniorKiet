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

class CourseDocumentProductAttribute implements DataPatchInterface
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
     * @return \Magenest\Attachment\Setup\Patch\Data\CourseTimelineProductAttribute|void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $this->addCourseTimelineProductAttribute($eavSetup);
    }

    /**
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function addCourseTimelineProductAttribute(EavSetup $eavSetup)
    {
        if (!$eavSetup->getAttribute(Product::ENTITY, self::COURSE_DOCUMENT)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                self::COURSE_DOCUMENT,
                [
                    'label' => 'Document',
                    'global' => Attribute::SCOPE_WEBSITE_TEXT,
                    'visible' => true,
                    'type' => 'text',
                    'required' => false,
                    'user_defined' => false,
                    'sort_order' => 500,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => \Magenest\Attachment\Model\Product\Type\CourseProductType::TYPE_CODE
                ]
            );
        }
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