<?php
namespace Magenest\Attachment\Model;


use Magento\Framework\Model\AbstractModel;

class Attachment extends AbstractModel
{
    public function _construct()
    {
        $this->_init(\Magenest\Attachment\Model\ResourceModel\Attachment::class);
    }
}