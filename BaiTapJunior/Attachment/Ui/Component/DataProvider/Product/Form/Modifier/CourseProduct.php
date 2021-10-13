<?php


namespace Magenest\Attachment\Ui\Component\DataProvider\Product\Form\Modifier;


use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

/**
 * Class Course
 * @package Magenest\Attachment\Ui\Component\DataProvider\Product\Form\Modifier
 */
class CourseProduct extends AbstractModifier
{
    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    protected $_arrayManager;
    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    private   $_locator;
    /**
     * @var null
     */
    private   $_productModel = null;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Course constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Stdlib\ArrayManager $arrayManager
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magento\Catalog\Model\Locator\LocatorInterface $locator
    ) {
        $this->_arrayManager = $arrayManager;
        $this->_locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct()
    {
        return $this->_productModel ?? $this->_productModel = $this->_locator->getProduct();
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $model = $this->getProduct();
        if ($model->getTypeId() === \Magenest\Attachment\Model\Product\Type\CourseProductType::TYPE_CODE) {
            $this->customizeCourseTimeline($meta);
            $this->customizeCourseDocument($meta);
        }
        return $meta;
    }

    /**
     * @param $meta
     */
    private function customizeCourseTimeline(&$meta)
    {
        $path = $this->_arrayManager->findPath(
            \Magenest\Attachment\Setup\Patch\Data\CourseTimelineProductAttribute::COURSE_TIMELINE,
            $meta,
            null,
            'children'
        );
        if ($path) {
            $meta = $this->_arrayManager->merge(
                $path . self::META_CONFIG_PATH,
                $meta,
                [
                    'componentType' => DynamicRows::NAME,
                    'label' => __('Course Timeline'),
                    'defaultRecord' => true,
                ]
            );

            $meta = $this->_arrayManager->set(
                $path . '/children',
                $meta,
                ['record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                            ],
                        ],
                    ],
                    'children' => [
                        'from_time' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'label' => __('From'),
                                        'additionalClasses' => 'admin__field-small',
                                        'component' => 'Magento_Ui/js/form/element/date',
                                        'options' => [
                                            'dateFormat' => 'HH:mm',
                                            'timeFormat' => 'HH:mm',
                                            'showsTime' => true,
                                            'timeOnly' => true,
                                            'currentText' => 'Now'
                                        ],
                                        'required' => true,
                                        'validation' => [
                                            'required-entry' => true
                                        ]
                                    ],
                                ],
                            ],
                            'attributes' => [
                                'class' => Field::class,
                                'name' => 'from_time',
                                'formElement' => Input::NAME
                            ]
                        ],
                        'to_time' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'label' => __('To'),
                                        'additionalClasses' => 'admin__field-small',
                                        'component' => 'Magento_Ui/js/form/element/date',
                                        'options' => [
                                            'dateFormat' => 'HH:mm',
                                            'timeFormat' => 'HH:mm',
                                            'showsTime' => true,
                                            'timeOnly' => true,
                                            'currentText' => 'Now'
                                        ],
                                        'required' => true,
                                        'validation' => [
                                            'required-entry' => true
                                        ]
                                    ],
                                ],
                            ],
                            'attributes' => [
                                'class' => Field::class,
                                'name' => 'to_time',
                                'formElement' => Input::NAME
                            ]
                        ],
                        'quantity' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Quantity'),
                                        'additionalClasses' => 'admin__field-x-small',
                                        'dataScope' => 'quantity',
                                        'required' => true,
                                        'validation' => [
                                            'validate-integer' => true,
                                            'validate-greater-than-zero' => true,
                                            'required-entry' => true
                                        ]
                                    ],
                                ],
                            ],
                            'attributes' => [
                                'class' => Field::class,
                                'name' => 'quantity',
                                'formElement' => Input::NAME
                            ]
                        ]
                    ],
                ]]
            );

            $meta = $this->_arrayManager->set(
                $path . '/children/record/children/actionDelete' . self::META_CONFIG_PATH,
                $meta,
                [
                    'componentType' => 'actionDelete',
                    'dataType' => Text::NAME,
                    'label' => ''
                ]
            );
        }
    }

    /**
     * @param $meta
     */
    private function customizeCourseDocument(&$meta)
    {
        $path = $this->_arrayManager->findPath(
            \Magenest\Attachment\Setup\Patch\Data\CourseDocumentProductAttribute::COURSE_DOCUMENT,
            $meta,
            null,
            'children'
        );
        if ($path) {
            $meta = $this->_arrayManager->merge(
                $path . self::META_CONFIG_PATH,
                $meta,
                [
                    'formElement' => Input::NAME,
                    'componentType' => Field::NAME,
                    'component' => 'Magenest_Attachment/js/form/element/course-document',
                    'elementTmpl' => 'Magenest_Attachment/ui/form/element/course-document',
                    'dataType' => Text::NAME,
                    'label' => __('Document'),
                    'notice' => 'Please enter at least 3 characters to show the suggestions.',
                    'searchUrl' => $this->urlBuilder->getUrl('magenest_attachment/attachment/search')
                ]
            );
        }
    }
}
