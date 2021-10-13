<?php


namespace Magenest\Attachment\Ui\Component\DataProvider;


use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class Attachment
 * @package Magenest\Attachment\Ui\Component\DataProvider
 */
class AttachmentData extends AbstractDataProvider
{
    /**
     * @var
     */
    private   $loadedData;
    /**
     * @var \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollection;

    /**
     * Attachment constructor.
     * @param \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Magenest\Attachment\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollection,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->attachmentCollection = $attachmentCollection;
        $this->collection = $this->attachmentCollection->create();
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }
        return $this->loadedData;
    }
}