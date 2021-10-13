<?php


namespace Magenest\OrderCancel\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_REASON_CANCELLATION = 'sales/order_cancellation_reason/reason';

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function getReasonCancel() {
        return json_decode(
            $this->scopeConfig->getValue(self::XML_REASON_CANCELLATION,\Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ), true);
    }
}
