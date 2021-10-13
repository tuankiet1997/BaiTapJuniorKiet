<?php


namespace Magenest\OrderCancel\Model;


class Cancellation extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Magenest\OrderCancel\Model\ResourceModel\Cancellation::class);
    }
}
