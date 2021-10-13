<?php


namespace Magenest\OrderCancel\Ui\Component\Listing\Column;


class OrderId extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $orderRepository;
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $order = $this->orderRepository->get($item[$this->getData('name')]);
                $item[$this->getData('name')] = '<a class="action-menu-item" target="_blank"
                                                                href="'.$this->urlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()]).'">'.$order->getIncrementId().'</a>';
            }
        }
        return $dataSource;
    }
}
