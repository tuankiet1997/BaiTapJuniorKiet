<?php

namespace Magenest\RedirectPage\Helper;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Translate\Inline\ParserInterface;
use Magento\Framework\Translate\InlineInterface;
use Magento\Theme\Controller\Result\MessagePlugin;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as SearchCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

/**
 * Class Data
 *
 * @package Magenest\VNPAY\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductCollection
     */
    protected $productCollection;

    /**
     * @var SearchCollection
     */
    protected $searchCollection;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $urlRewriteCollectionFactory;

    protected $inlineTranslate;

    protected $cookieMetadataFactory;

    protected $cookieManager;

    protected $serializer;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    protected $interpretationStrategy;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductCollection $productCollection
     * @param SearchCollection $searchCollection
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param InlineInterface|null $inlineTranslate
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param ManagerInterface $messageManager
     * @param \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $interpretationStrategy
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        ProductCollection $productCollection,
        SearchCollection $searchCollection,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        InlineInterface $inlineTranslate = null,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        ManagerInterface $messageManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $interpretationStrategy
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->productCollection = $productCollection;
        $this->searchCollection = $searchCollection;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->messageManager = $messageManager;
        $this->inlineTranslate = $inlineTranslate ?: ObjectManager::getInstance()->get(InlineInterface::class);
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->interpretationStrategy = $interpretationStrategy;
        parent::__construct($context);
    }

    /**
     * Search category by key
     * @param $key
     * @return \Magento\Framework\DataObject
     */
    public function searchCategory($key)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('name', ['like' => '%' . $key . '%']);
        return $collection->getFirstItem();
    }

    /**
     * Search product by name
     * @param $key
     * @return \Magento\Framework\DataObject
     */
    public function searchProduct($key)
    {
        $collection = $this->productCollection->create();
        $collection->addFieldToFilter('name', ['like' => '%' . $key . '%']);
        return $collection->getFirstItem();
    }

    /**
     * Search term
     * @param $key
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function searchSearchTerm($key): \Magento\Framework\DataObject
    {
        $currentStoreId = $this->storeManager->getStore()->getId();
        $collection = $this->searchCollection->create();
        $collection->addFieldToFilter('query_text', ['like' => '%' . $key . '%']);
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('store_id', $currentStoreId);
        return $collection->getFirstItem();
    }

    /**
     * @param $type
     * @param $id
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRequestPath($type, $id)
    {
        $currentStoreId = $this->storeManager->getStore()->getId();
        $urlRewrite = $this->urlRewriteCollectionFactory->create()
            ->addFieldToFilter('entity_type', $type)
            ->addFieldToFilter('entity_id', $id)
            ->addFieldToFilter('store_id', $currentStoreId)
            ->getFirstItem();
        if ($urlRewrite->getUrlRewriteId()) {
            return $urlRewrite->getRequestPath();
        } else {
            $requestPath = 'catalog/' . $type . '/view/id/' . $id;
        }
        return $requestPath;
    }

    public function setCookie(array $messages)
    {
        if (!empty($messages)) {
            if ($this->inlineTranslate->isAllowed()) {
                foreach ($messages as &$message) {
                    $message['text'] = $this->convertMessageText($message['text']);
                }
            }

            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $publicCookieMetadata->setDurationOneYear();
            $publicCookieMetadata->setPath('/');
            $publicCookieMetadata->setHttpOnly(false);

            $this->cookieManager->setPublicCookie(
                MessagePlugin::MESSAGES_COOKIES_NAME,
                $this->serializer->serialize($messages),
                $publicCookieMetadata
            );
        }
    }

    /**
     * Replace wrapping translation with html body.
     *
     * @param string $text
     * @return string
     */
    public function convertMessageText(string $text): string
    {
        if (preg_match('#' . ParserInterface::REGEXP_TOKEN . '#', $text, $matches)) {
            $text = $matches[1];
        }

        return $text;
    }

    /**
     * Return messages array and clean message manager messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->getCookiesMessages();
        /** @var MessageInterface $message */
        foreach ($this->messageManager->getMessages(true)->getItems() as $message) {
            $messages[] = [
                'type' => $message->getType(),
                'text' => $this->interpretationStrategy->interpret($message),
            ];
        }
        return $messages;
    }

    /**
     * Return messages stored in cookies
     *
     * @return array
     */
    public function getCookiesMessages()
    {
        $messages = $this->cookieManager->getCookie(MessagePlugin::MESSAGES_COOKIES_NAME);
        if (!$messages) {
            return [];
        }
        $messages = $this->serializer->unserialize($messages);
        if (!is_array($messages)) {
            $messages = [];
        }
        return $messages;
    }
}
