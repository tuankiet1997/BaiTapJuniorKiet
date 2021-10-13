<?php


namespace Magenest\OrderCancel\Api;


/**
 * Interface CustomerOrderRepositoryInterface
 * @package Magenest\OrderCancellationReason\Api
 */
interface OrderRepositoryInterface
{
    /**
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getList();

    /**
     * @param int $id
     * @param string $reason
     * @param string $comment
     * @return \Magento\Framework\Phrase | string | bool
     */
    public function cancelOrder($id, $reason, $comment);

    /**
     * @param int $id
     * @return \Magento\Sales\Api\Data\OrderInterface | bool
     */
    public function getDetail($id);
}
