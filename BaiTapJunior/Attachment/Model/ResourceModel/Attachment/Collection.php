<?php


namespace Magenest\Attachment\Model\ResourceModel\Attachment;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(\Magenest\Attachment\Model\Attachment::class, \Magenest\Attachment\Model\ResourceModel\Attachment::class);
    }
}