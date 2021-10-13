<?php


namespace Magenest\OrderCancel\Model;


class OrderRepository implements  \Magenest\OrderCancel\Api\OrderRepositoryInterface
{
    protected $orderCollectionFactory;

    protected $orderConfig;

    protected $compositeUserContext;

    protected $cancellationFactory;

    protected $cancellationResource;

    protected $orderHistoryFactory;

    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magenest\OrderCancel\Model\ResourceModel\Cancellation $cancellationResource,
        \Magenest\OrderCancel\Model\CancellationFactory $cancellationFactory,
        \Magento\Authorization\Model\CompositeUserContext $compositeUserContext,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderConfig = $orderConfig;
        $this->compositeUserContext = $compositeUserContext;
        $this->cancellationFactory = $cancellationFactory;
        $this->cancellationResource = $cancellationResource;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->orderRepository = $orderRepository;
    }

    public function getList()
    {
        $orders = $this->orderCollectionFactory->create($this->getCustomerId())->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'status',
            ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
        )->setOrder(
            'created_at',
            'desc'
        );
        return $orders->getItems();
    }

    public function getCustomerId() {
        return $this->compositeUserContext->getUserId();
    }

    public function cancelOrder(
        $id,
        $reason,
        $comment
    ) {
        $order = $this->orderCollectionFactory->create($this->getCustomerId())->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'entity_id',
            ['eq' => $id]
        )->addFieldToFilter(
            'status',
            ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
        )->setOrder(
            'created_at',
            'desc'
        )->getFirstItem();
        $cancellation = $this->cancellationFactory->create();
        if ($order->getId()) {
            $this->cancellationResource->load($cancellation,$order->getId(),'order_id');
            $cancellation->setOrderId($order->getIncrementId());
            $cancellation->setComment($comment);
            $cancellation->setReason($reason);
            $cancellation->setStoreId($order->getStoreId());
            $cancellation->setCustomerEmail($order->getCustomerEmail());
            $this->cancellationResource->save($cancellation);
            if ($order->canComment()) {
                $history = $this->orderHistoryFactory->create()
                    ->setStatus($order->getStatus())
                    ->setEntityName(\Magento\Sales\Model\Order::ENTITY)
                    ->setComment(__('Cancellation reason: %1. Comment: %2', $reason, $comment));

                $history->setIsCustomerNotified(true)
                    ->setIsVisibleOnFront(true);
                $order->addStatusHistory($history);
                $this->orderRepository->save($order);
            }
            return true;
        }
        return __('Could not found order.');
    }

    public function getDetail($id)
    {
        $orders = $this->orderCollectionFactory->create($this->getCustomerId())->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'entity_id',
            ['eq' => $id]
        )->addFieldToFilter(
            'status',
            ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
        )->setOrder(
            'created_at',
            'desc'
        );
        return count($orders) ? $orders->getFirstItem() : false;
    }
}
