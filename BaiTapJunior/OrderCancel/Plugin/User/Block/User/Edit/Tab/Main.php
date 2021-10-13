<?php

namespace Magenest\OrderCancel\Plugin\User\Block\User\Edit\Tab;

class Main
{
    protected $registry;

    protected $websitesOptionsProvider;

    public function __construct(
        \Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider $websitesOptionsProvider,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->websitesOptionsProvider = $websitesOptionsProvider;
    }

    public function beforeSetForm(\Magento\User\Block\User\Edit\Tab\Main $subject, \Magento\Framework\Data\Form $form) {
        $model = $this->registry->registry('permissions_user');
        $fieldset = $form->getElement('base_fieldset');
        if ($fieldset && !$form->getElement('website_role')) {
            $fieldset->addField(
                'website_role',
                'multiselect',
                [
                    'name' => 'website_role',
                    'label' => __('Website Role'),
                    'id' => 'website_role',
                    'title' => __('Website Role'),
                    'values' => $this->websitesOptionsProvider->toOptionArray(),
                    'class' => 'select'
                ]
            );
        }
        $form->addValues(
            [
                'website_role' => $model->getData('website_role'),
            ]
        );
        return [$form];
    }
}
