<?php


namespace Magenest\OrderCancel\Controller\Cancellation;


class Submit extends \Magento\Framework\App\Action\Action
{
    protected $jsonResult;

    protected $orderRepository;

    protected $cancellationReasonFactory;

    protected $cancellationReasonResource;

    protected $orderHistoryFactory;

    protected $messageManager;

    public function __construct(
        \Magento\Framework\Message\Manager $messageManager,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magenest\OrderCancel\Model\ResourceModel\Cancellation $cancellationReasonResource,
        \Magenest\OrderCancel\Model\CancellationFactory $cancellationReasonFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->jsonResult = $jsonResult;
        $this->orderRepository = $orderRepository;
        $this->cancellationReasonFactory = $cancellationReasonFactory;
        $this->cancellationReasonResource = $cancellationReasonResource;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $result = $this->jsonResult->create();
        $orderId = $this->getRequest()->getParam('order_id');
        $reason = $this->getRequest()->getParam('reason');
        $comment = $this->getRequest()->getParam('comment');
        $order = $this->orderRepository->get($orderId);
        $cancellationReason = $this->cancellationReasonFactory->create();
        try {
            if ($order->getId()) {
                $this->cancellationReasonResource->load($cancellationReason,$order->getId(),'order_id');
                $cancellationReason->setOrderId($order->getIncrementId());
                $cancellationReason->setComment($comment);
                $cancellationReason->setReason($reason);
                $cancellationReason->setStoreId($order->getStoreId());
                $cancellationReason->setCustomerEmail($order->getCustomerEmail());
                $this->cancellationReasonResource->save($cancellationReason);
                $this->messageManager->addSuccessMessage(__('Your cancellation request has been submitted.'));
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
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Could not found order'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Could not submit cancellation %1', $exception->getMessage()));
        }
        $result->setData(true);
        return $result;
    }
}
