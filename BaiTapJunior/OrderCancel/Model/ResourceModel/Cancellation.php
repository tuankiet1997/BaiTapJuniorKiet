<?php


namespace Magenest\OrderCancel\Model\ResourceModel;


class Cancellation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('magenest_order_cancel','id');
    }
}
