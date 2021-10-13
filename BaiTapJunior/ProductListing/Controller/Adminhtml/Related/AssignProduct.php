<?php


namespace Magenest\ProductListing\Controller\Adminhtml\Related;


use Magento\Backend\App\Action;

class AssignProduct extends Action
{
    protected $logger;
    protected $filter;
    protected $cache;
    protected $productCollection;
    protected $productRepository;
    protected $productLink;

    public function __construct(
        \Magento\Catalog\Model\ProductLink\LinkFactory $productLink,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\Cache\TypeListInterface $cache,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->filter = $filter;
        $this->cache = $cache;
        $this->productCollection = $productCollection;
        $this->productRepository = $productRepository;
        $this->productLink = $productLink;
    }

    public function execute()
    {
        try{
            $idsProduct = explode(',',$this->getRequest()->getParam('ids'));
            $productCollection = $this->filter->getCollection($this->productCollection->create());
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($productCollection as $product) {
                $links = $product->getProductLinks();
                foreach ($idsProduct as $id) {
                    if ($product->getId() != $id) {
                        $productAssign= $this->productRepository->getById($id);
                        $productLink = $this->productLink->create();
                        $productLink->setSku($product->getSku())
                            ->setLinkedProductSku($productAssign->getSku())
                            ->setLinkType("related");
                        $links[] = $productLink;
                    }
                }
                $product->setProductLinks($links);
                $this->productRepository->save($product);
            }
            $this->messageManager->addSuccessMessage(__('Assign product has been successful'));
        }catch (\Exception $exception){
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->critical($exception->getMessage());
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('catalog/product/index');
    }
}
