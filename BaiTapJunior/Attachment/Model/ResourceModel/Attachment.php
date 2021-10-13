<?php


namespace Magenest\Attachment\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Attachment extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('magenest_attachment','id');
    }
}