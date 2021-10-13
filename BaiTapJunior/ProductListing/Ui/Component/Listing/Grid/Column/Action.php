<?php

namespace Magenest\ProductListing\Ui\Component\Listing\Grid\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Action extends Column
{

    CONST VISIBILITY_ID = '1';
    protected $productResource;
    protected $product;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productResource = $productResource;
        $this->product = $product;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $product = $this->product->create();
                $this->productResource->load($product,$item['entity_id']);
                if($product->getVisibility() != self::VISIBILITY_ID ){
                    $item[$this->getData('name')]['view'] = [
                        'href' => $product->getProductUrl(),
                        'label' => __('View'),
                        'hidden' => false,
                        'target' => '_blank'
                    ];
                }
            }
        }
        return $dataSource;
    }
}
