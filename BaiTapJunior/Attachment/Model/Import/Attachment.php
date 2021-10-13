<?php
namespace Magenest\Attachment\Model\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Attachment
 * @package Magenest\Attachment\Model\Import
 */
class Attachment extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    /**
     *
     */
    const ENTITY_CODE     = 'magenest_attachment';
    /**
     *
     */
    const TABLE                        = 'magenest_attachment';
    /**
     *
     */
    const FILE_NAME                      = 'file_name';
    /**
     *
     */
    const FILE_EXTENSION                  = 'file_extension';
    /**
     *
     */
    const MINE_TYPE                       = 'mine_type';
    /**
     *
     */
    const FILE_SIZE                       = 'file_size';
    /**
     *
     */
    const FILE_LABEL                      = 'file_label';
    /**
     *
     */
    const FILE_DETAIL                     = 'file_detail';
    /**
     *
     */
    const CUSTOMER_GROUPS                 = 'customer_group_ids';
    /**
     *
     */
    const VISIBLE                         = 'status';
    /**
     *
     */
    const INCLUDE_ORDER                   = 'include_order';
    /**
     *
     */
    const ERROR_CODE_FILE_NAME_IS_NULL    = 'file_name is required';
    /**
     *
     */
    const ERROR_CODE_DUPLICATE_FILE_NAME  = 'Duplicate file name';
    /**
     *
     */
    const ERROR_CODE_DUPLICATE_FILE_LABEL = 'Duplicate file label';

    /**
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var string[]
     */
    protected $validColumnNames
        = [
            self::FILE_NAME,
            self::FILE_LABEL,
            self::CUSTOMER_GROUPS,
            self::VISIBLE,
            self::INCLUDE_ORDER,
        ];

    /**
     * @var string[]
     */
    protected $_uniqueAttributes
        = [
            self::FILE_NAME,
            self::FILE_LABEL,
        ];

    /**
     * @var \Magenest\Attachment\Model\ResourceModel\Attachment
     */
    protected $attachmentResource;

    /**
     * @var \Magenest\Attachment\Model\AttachmentFactory
     */
    protected $attachment;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\File\Mime
     */
    protected $mime;

    /**
     * @var \Magenest\Attachment\Helper\DataHelper
     */
    protected $attachmentHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Attachment constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magenest\Attachment\Helper\AttachmentHelper $attachmentHelper
     * @param \Magento\Framework\File\Mime $mime
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magenest\Attachment\Model\ResourceModel\Attachment $attachmentResource
     * @param \Magenest\Attachment\Model\AttachmentFactory $attachment
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magenest\Attachment\Helper\DataHelper $attachmentHelper,
        \Magento\Framework\File\Mime $mime,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator,
        \Magenest\Attachment\Model\ResourceModel\Attachment $attachmentResource,
        \Magenest\Attachment\Model\AttachmentFactory $attachment
    ) {
        $this->urlBuilder         = $urlBuilder;
        $this->attachmentHelper   = $attachmentHelper;
        $this->mime               = $mime;
        $this->filesystem         = $filesystem;
        $this->jsonHelper         = $jsonHelper;
        $this->_importExportData  = $importExportData;
        $this->_resourceHelper    = $resourceHelper;
        $this->_dataSourceModel   = $importData;
        $this->_connection        = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator    = $errorAggregator;
        $this->attachmentResource = $attachmentResource;
        $this->attachment         = $attachment;
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'magenest_attachment';
    }

    /**
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(
        array $rowData,
              $rowNum
    ) {
        if (!$rowData[self::FILE_NAME]) {
            $this->addRowError(__(self::ERROR_CODE_FILE_NAME_IS_NULL), $rowNum, self::FILE_NAME);
        }

        if (isset($this->_uniqueAttributes[self::FILE_NAME][$rowData[self::FILE_NAME]])) {
            $this->addRowError(self::ERROR_CODE_DUPLICATE_FILE_NAME, $rowNum, self::FILE_NAME);
        } else {
            $this->_uniqueAttributes[self::FILE_NAME][$rowData[self::FILE_NAME]] = $rowData[self::FILE_NAME];
        }

        if ($rowData[self::FILE_LABEL] && isset($this->_uniqueAttributes[self::FILE_LABEL][$rowData[self::FILE_LABEL]])) {
            $this->addRowError(self::ERROR_CODE_DUPLICATE_FILE_LABEL, $rowNum, self::FILE_LABEL);
        } else {
            $this->_uniqueAttributes[self::FILE_LABEL][$rowData[self::FILE_LABEL]] = $rowData[self::FILE_LABEL];
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * @return bool
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveAttachment();
        }
        return true;
    }

    /**
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function saveAttachment()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $row) {
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $this->updateRowData($row);
                $attachment                 = $this->attachment->create();
                $this->attachmentResource->load($attachment, $row[self::FILE_NAME], self::FILE_NAME);
                if ($attachment->getId()) {
                    $attachment->addData($row);
                    $this->attachmentResource->save($attachment);
                    $this->countItemsUpdated ++;
                    continue;
                }
                $entityList[$rowNum] = $row;
                $this->countItemsCreated ++;
            }

            $this->saveEntityFinish($entityList);
        }
    }

    /**
     * @param $row
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function updateRowData(&$row)
    {
        $filePath                  = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath('magenest/attachment/' . $row[self::FILE_NAME]);
        $pathInfo                  = pathinfo($filePath);
        $row[self::MINE_TYPE]      = $this->mime->getMimeType($filePath);
        $row[self::FILE_SIZE]      = filesize($filePath);
        $row[self::FILE_EXTENSION] = $pathInfo['extension'];
        $fileDetail                = [
            'name'           => $row[self::FILE_NAME],
            'type'           => $row[self::MINE_TYPE],
            'size'           => $row[self::FILE_SIZE],
            'path'           => $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath('magenest/attachment/'),
            'file'           => $row[self::FILE_NAME],
            'link_file'      => $this->urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'magenest/attachment/' . $row[self::FILE_NAME],
            'icon_extension' => $this->attachmentHelper->getIconExtension($row[self::FILE_EXTENSION]),
            'previewType'    => 'image',
        ];
        $row[self::FILE_NAME]      = $pathInfo['filename'];
        $row[self::FILE_LABEL]     = $row[self::FILE_LABEL] ?? $row[self::FILE_NAME];
        $row[self::CUSTOMER_GROUPS] = json_encode(explode(',', $row[self::CUSTOMER_GROUPS]) ?? '32000');
        $row[self::FILE_DETAIL]     = json_encode($fileDetail);
    }

    /**
     * @param array $entityData
     * @return bool
     */
    private function saveEntityFinish(array $entityData): bool
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName(static::TABLE);
            $this->_connection->insertMultiple($tableName, $entityData);
        }
        return true;
    }
}
