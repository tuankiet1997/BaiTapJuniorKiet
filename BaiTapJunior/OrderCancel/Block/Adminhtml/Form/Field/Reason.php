<?php

namespace Magenest\OrderCancel\Block\Adminhtml\Form\Field;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Reason extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('reason', ['label' => __('Reason'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
