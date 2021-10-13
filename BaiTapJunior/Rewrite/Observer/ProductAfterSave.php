<?php

namespace Magenest\Rewrite\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory;

/**
 * Class ProductSaveAfter
 * @package Magenest\ControllerAndUrlRewrite\Observer
 */
class ProductAfterSave implements ObserverInterface
{
    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    protected $flag;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $urlRewriteCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * ProductSaveAfter constructor.
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param CollectionFactory $collectionFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     */
    public function __construct(
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        CollectionFactory $collectionFactory,
        UrlRewriteFactory $urlRewriteFactory
    ) {
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->collectionFactory = $collectionFactory;
        $this->flag = 1;
    }

    public function execute(Observer $observer)
    {
        if ($this->flag) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $observer->getProduct();
            $specialPrice = $product->getSpecialPrice();
            if ($specialPrice) {
                $basePrice = $product->getPrice();
                if ($specialPrice < $basePrice) {
                    $this->createUrlRewrite($product->getSku(), $product->getId());
                }
            }
            $this->flag = 0;
        }
    }

    /**
     * Create url rewrite
     * @param $sku
     * @param $id
     */
    protected function createUrlRewrite($sku, $id)
    {
        try {
            $urlRewriteModel = $this->urlRewriteCollectionFactory->create()
                ->addFieldToFilter('entity_type', 'product')
                ->addFieldToFilter('request_path', ['like' => '%sale/' . $sku . '%'])
                ->addFieldToFilter('store_id', 0)
                ->getFirstItem();
            $storeIds = $this->collectionFactory->create()->getAllIds();
            if (!$urlRewriteModel->getUrlRewriteId()) {
                $urlRewrite = $this->urlRewriteFactory->create();
                foreach ($storeIds as $storeId) {
                    $urlRewrite->unsetData();
                    $page = array(
                        'url_rewrite_id' => null,
                        'entity_type'    => 'product',
                        'entity_id'      => $id,
                        'request_path'   => 'sale/' . $sku,
                        'target_path'    => 'catalog/product/view/id/' . $id,
                        'store_id'       => $storeId
                    );
                    $urlRewrite->setData($page);
                    $urlRewrite->save();
                }
            }
        } catch (\Exception $exception) {}
    }
}
