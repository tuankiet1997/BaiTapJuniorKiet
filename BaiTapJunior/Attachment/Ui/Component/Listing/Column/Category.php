<?php

namespace Magenest\Attachment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Category
 * @package Magenest\Attachment\Ui\Component\Listing\Column
 */
class Category extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var \Magento\Catalog\Model\ProductCategoryList
     */
    private $productCategory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\ProductCategoryList $productCategory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductCategoryList $productCategory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productCategory = $productCategory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $productId = $item['entity_id'];
                $categoryIds = $this->getCategoryIds($productId);
                $categories = [];
                if (count($categoryIds)) {
                    foreach ($categoryIds as $categoryId) {
                        $categoryData = $this->categoryRepository->get($categoryId);
                        $categories[] = $categoryData->getName();
                    }
                }
                $item[$fieldName] = implode(', ', $categories);
            }
        }
        return $dataSource;
    }

    /**
     * @param int $productId
     * @return array
     */
    private function getCategoryIds(int $productId)
    {
        $categoryIds = $this->productCategory->getCategoryIds($productId);
        $category = [];
        if ($categoryIds) {
            $category = array_unique($categoryIds);
        }
        return $category;
    }
}
