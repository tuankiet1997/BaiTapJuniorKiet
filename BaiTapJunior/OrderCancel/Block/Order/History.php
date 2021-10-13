<?php


namespace Magenest\OrderCancel\Block\Order;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

class History extends \Magento\Sales\Block\Order\History
{
    private $orderCollectionFactory;

    protected $orderRepository;

    protected $dataHelper;

    protected $cancellationFactory;

    protected $cancellationResource;

    protected $cancellationCollectionFactory;

    public function __construct(
        \Magenest\OrderCancel\Model\ResourceModel\Cancellation\CollectionFactory $cancellationCollectionFactory,
        \Magenest\OrderCancel\Model\ResourceModel\Cancellation $cancellationResource,
        \Magenest\OrderCancel\Model\CancellationFactory $cancellationFactory,
        \Magenest\OrderCancel\Helper\Data $dataHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
        $this->orderRepository = $orderRepository;
        $this->dataHelper = $dataHelper;
        $this->cancellationFactory = $cancellationFactory;
        $this->cancellationResource = $cancellationResource;
        $this->cancellationCollectionFactory = $cancellationCollectionFactory;
    }

    public function canCancel($orderId) {
        $cancellationReasonCollection = $this->cancellationCollectionFactory->create()
            ->addFieldToFilter('order_id',$orderId);
        return $this->orderRepository->get($orderId)->canCancel() && count($cancellationReasonCollection) == 0;
    }

    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = $this->orderCollectionFactory->create();
        }
        return $this->orderCollectionFactory;
    }

    public function getReasonCancel() {
        return $this->dataHelper->getReasonCancel();
    }
}
