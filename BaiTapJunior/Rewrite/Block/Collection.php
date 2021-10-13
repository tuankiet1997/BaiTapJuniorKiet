<?php

namespace Magenest\Rewrite\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

/**
 * Class Collection
 * @package Magenest\ControllerAndUrlRewrite\Block
 */
class Collection extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Image
     */
    protected $imageBuilder;

    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $urlCollection;

    /**
     * Collection constructor.
     * @param UrlRewriteCollectionFactory $urlCollection
     * @param StoreManagerInterface $storeManager
     * @param Data $priceHelper
     * @param Image $imageBuilder
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $collectionFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        UrlRewriteCollectionFactory $urlCollection,
        StoreManagerInterface $storeManager,
        Data $priceHelper,
        Image $imageBuilder,
        ResourceConnection $resourceConnection,
        CollectionFactory $collectionFactory,
        Template\Context $context,
        array $data = []
    ) {
        $this->urlCollection = $urlCollection;
        $this->storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
        $this->imageBuilder = $imageBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    /**
     * Get sale image
     * @return string
     */
    public function getSaleImage()
    {
        return $this->getViewFileUrl('Magenest_Rewrite::images/vn.png');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = $connection->select()
            ->from($this->resourceConnection->getTableName('url_rewrite'), 'entity_id')
            ->where(new \Zend_Db_Expr("request_path LIKE 'sale/%'"))
            ->distinct();
        $productIds = $connection->fetchCol($sql);
        return $this->collectionFactory->create()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter('entity_id', ['in' => $productIds])->setPageSize(10);
    }

    /**
     * Get image url of product
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getImageUrl(\Magento\Catalog\Model\Product $product)
    {
        return $this->imageBuilder->init($product, 'product_thumbnail_image')
            ->setImageFile($product->getFile())->resize(240, 300)->getUrl();
    }

    /**
     * Format price
     * @param $price
     * @return float|string
     */
    public function formatPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * Get url rewrite of product
     * @param $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlRewrite($productId)
    {
        $currentStoreId = $this->storeManager->getStore()->getId();
        $urlwrite = $this->urlCollection->create()
            ->addFieldToFilter('entity_type', 'product')
            ->addFieldToFilter('entity_id', $productId)
            ->addFieldToFilter('store_id', $currentStoreId)
            ->addFieldToFilter('request_path', ['like' => 'sale/%']);
        $requestPath = $urlwrite->getFirstItem()->getRequestPath();
        return $this->storeManager->getStore()->getBaseUrl() . $requestPath;
    }
}
