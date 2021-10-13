<?php
namespace Magenest\OrderExport\Controller\Adminhtml\Order;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Export extends Action
{
    protected $logger;
    protected $filter;
    protected $orderCollection;
    protected $fileResponse;
    protected $csvProcessor;
    protected $directoryList;
    protected $fileFactory;

    public function __construct(
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Backend\App\Response\Http\FileFactory $fileResponse,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->filter = $filter;
        $this->orderCollection = $orderCollection;
        $this->fileResponse = $fileResponse;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        try{
            $fileName = 'export_order.csv';
            $filePath =  $this->directoryList->getPath(DirectoryList::MEDIA) . "/" . $fileName;
            $orderCollection = $this->filter->getCollection($this->orderCollection->create());
            $content[] = [
                __('Order Id'),
                __('Sku'),
                __('Product Name'),
                __('Qty (Ordered qty)'),
                __('Order Status'),
                __('Item Status'),
                __('Tax Amount'),
                __('Discount Amount'),
                __('Customer Email'),
                __('Purchase Time'),
                __('Store Name'),
                __('Purchase Date'),
                __('Bill-to Name'),
                __('Ship-to Name'),
                __('Payment method'),
                __('Line total'),
                __('Coupon code'),
                __('Promotion Name'),
                __('Order comment'),
            ];
            /** @var \Magento\Sales\Model\Order $order */
            foreach ($orderCollection as $order) {
                $orderComment = [];
                foreach ($order->getStatusHistoryCollection() as $status) {
                    if ($status->getComment()) {
                        $orderComment[] = $status->getComment();
                    }
                }
                foreach ($order->getAllItems() as $item) {
                    $content[] = [
                        $order->getIncrementId(),
                        $item->getSku(),
                        $item->getName(),
                        (int) $item->getQtyOrdered(),
                        $order->getStatusLabel(),
                        $item->getStatus()->getText(),
                        $item->getTaxAmount(),
                        $item->getDiscountAmount(),
                        $order->getCustomerEmail(),
                        $order->getCreatedAt(),
                        $order->getStoreName(),
                        $order->getCreatedAt(),
                        $order->getBillingAddress()->getName(),
                        $order->getShippingAddress()->getName(),
                        $order->getPayment()->getMethod(),
                        $item->getRowTotal(),
                        $order->getCouponCode(),
                        $order->getCouponRuleName(),
                        implode(', ',$orderComment),
                    ];
                }
            }
            $this->csvProcessor->setEnclosure('"')->setDelimiter(',')->saveData($filePath, $content);
            return $this->fileFactory->create(
                $fileName,
                [
                    'type'  => "filename",
                    'value' => $fileName,
                    'rm'    => true,
                ],
                DirectoryList::MEDIA,
                'text/csv',
                null
            );
        }catch (\Exception $exception){
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->critical($exception->getMessage());
        }
    }
}
