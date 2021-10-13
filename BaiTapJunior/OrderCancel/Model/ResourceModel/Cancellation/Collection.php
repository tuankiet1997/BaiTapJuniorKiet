<?php
namespace Magenest\OrderCancel\Model\ResourceModel\Cancellation;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(\Magenest\OrderCancel\Model\Cancellation::class,\Magenest\OrderCancel\Model\ResourceModel\Cancellation::class);
    }
}
