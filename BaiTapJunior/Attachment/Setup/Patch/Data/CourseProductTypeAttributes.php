<?php

namespace Magenest\Attachment\Setup\Patch\Data;

use Magenest\Attachment\Model\Product\Type\CourseProductType;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CourseProductTypeAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return CourseProductTypeAttributes|void
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);

        $fieldList = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'cost',
            'tier_price',
            'weight',
        ];

        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $eavSetup->getAttribute(Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array(CourseProductType::TYPE_CODE, $applyTo)) {
                $applyTo[] = CourseProductType::TYPE_CODE;
                $eavSetup->updateAttribute(
                    Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }
    }

    /**
     * @return string[]|void
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return string[]|void
     */
    public function getAliases()
    {
        return [];
    }
}