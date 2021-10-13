<?php


namespace Magenest\Attachment\Block\Adminhtml\Attachment;


use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    protected $urlBuilder;
    protected $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    public function getButtonData()
    {
        $data = [];
        if ($this->request->getParam('id')) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->urlBuilder->getUrl('*/*/delete', ['id' => $this->request->getParam('id')]) . '\', {data: {}})',
            ];
        }
        return $data;
    }
}