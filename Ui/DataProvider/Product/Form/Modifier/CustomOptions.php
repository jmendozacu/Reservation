<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 08/07/2016
 * Time: 08:45
 */
namespace Magenest\Reservation\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;

class CustomOptions extends AbstractModifier
{
    const GROUP_BILLING_OPTIONS_SCOPE = 'data.product';

    protected $meta = [];
    protected $locator;
    protected $storeManager;
    protected $productOptionsConfig;
    protected $productOptionsPrice;
    protected $urlBuilder;
    protected $arrayManager;
    protected $_productFactory;
    protected $_productScheduleWithoutStaffFactory;

    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        \Magenest\Reservation\Model\ProductFactory $productFactory
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->productOptionsConfig = $productOptionsConfig;
        $this->productOptionsPrice = $productOptionsPrice;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->_productScheduleWithoutStaffFactory = $productScheduleWithoutStaffFactory;
        $this->_productFactory = $productFactory;
    }

    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        if (array_key_exists('magenest_reservation_type', $data[strval($productId)]['product']) && $data[strval($productId)]['product']['magenest_reservation_type'] == 1) {
            $thisProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->getFirstItem();
            if ($thisProduct) {
                if ($thisProduct->getNeedStaff() == 0) {
                    $data[strval($productId)]['product']['magenest_reservation_staff_option'] = 'yes';
                    if ($thisProduct->getOption() == 1) {
                        $data[strval($productId)]['product']['magenest_reservation_reservation_option_no_staff'] = 'reservationOption0';
                        $thisProductSchedule = $this->_productScheduleWithoutStaffFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
                        if ($thisProductSchedule) {
                            foreach ($thisProductSchedule as $thisProductScheduleItem) {
                                $fromArray = explode(':', $thisProductScheduleItem->getFromTime());
                                $toArray = explode(':', $thisProductScheduleItem->getToTime());
                                $data[strval($productId)]['product']['magenest_reservation_grid_option_0']['magenest_reservation_grid_option_0'][] =
                                    [
                                        'magenest_reservation_day' => $thisProductScheduleItem->getWeekday(),
                                        'magenest_reservation_from_hour' => $fromArray[0],
                                        'magenest_reservation_from_min' => $fromArray[1],
                                        'magenest_reservation_to_hour' => $toArray[0],
                                        'magenest_reservation_to_min' => $toArray[1],
                                        'magenest_reservation_slot' => $thisProductScheduleItem->getSlots()
                                    ];
                            }
                        }
                    } else {
                        $data[strval($productId)]['product']['magenest_reservation_reservation_option_no_staff'] = 'reservationOption1';

                        $thisProductSchedule = $this->_productScheduleWithoutStaffFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
                        if ($thisProductSchedule) {
                            foreach ($thisProductSchedule as $thisProductScheduleItem) {
                                $data[strval($productId)]['product']['magenest_reservation_grid_option_1']['magenest_reservation_grid_option_1'][] =
                                    [
                                        'magenest_reservation_day' => $thisProductScheduleItem->getWeekday(),
                                        'magenest_reservation_slot' => $thisProductScheduleItem->getSlots()
                                    ];
                            }
                        }
                    }
                } else {
                    $data[strval($productId)]['product']['magenest_reservation_staff_option'] = 'yes';
                    if ($thisProduct->getOption() == 0) {
                        $data[strval($productId)]['product']['magenest_reservation_reservation_option_need_staff'] = 'reservationOption0';
                    } else {
                        $data[strval($productId)]['product']['magenest_reservation_reservation_option_need_staff'] = 'reservationOption1';
                    }
                }
            }
        }
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->createReservationOptionsPanel();

        return $this->meta;
    }

    protected function createReservationOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                'magenest_reservation_configuration' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Magenest Reservation Configuration'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_BILLING_OPTIONS_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    'magenest-booking-and-reservation',
                                    200
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        'magenest_reservation_staff_option' => $this->getHeaderStaffOption(10),
                        'magenest_reservation_reservation_option_no_staff' => $this->getHeaderReservationOptionNoStaff(20),
                        'magenest_reservation_reservation_option_need_staff' => $this->getHeaderReservationOptionNeedStaff(30),
                        'magenest_reservation_grid_option_0' => $this->getReservationGridOption0(40),
                        'magenest_reservation_grid_option_1' => $this->getReservationGridOption1(50),
                    ]
                ]
            ]
        );

        return $this;
    }

    protected function getHeaderStaffOption($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Staff Necessary ? '),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'component' => 'Magento_Catalog/js/custom-options-type',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'selectType' => 'group',
                        'dataScope' => 'magenest_reservation_staff_option',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->getStaffOption(),
                        'disableLabel' => true,
                        'multiple' => false,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ]   ,
                        'groupsConfig' => [
                            'staffOption1' => [
                                'values' => ['no'],
                                'indexes' => [
                                    'magenest_reservation_reservation_option_no_staff'
                                ]
                            ],
                            'staffOption2' => [
                                'values' => ['yes'],
                                'indexes' => [
                                    'magenest_reservation_reservation_option_need_staff'
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getHeaderReservationOptionNoStaff($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Reservation Option ? '),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'component' => 'Magento_Catalog/js/custom-options-type',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'selectType' => 'group',
                        'dataScope' => 'magenest_reservation_reservation_option_no_staff',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->getReservationOption(),
                        'disableLabel' => true,
                        'multiple' => false,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                        'groupsConfig' => [
                            'reservationOption0' => [
                                'values' => ['reservationOption0'],
                                'indexes' => [
                                    'magenest_reservation_grid_option_0'
                                ]
                            ],
                            'reservationOption1' => [
                                'values' => ['reservationOption1'],
                                'indexes' => [
                                    'magenest_reservation_grid_option_1'
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getHeaderReservationOptionNeedStaff($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Reservation Option ? '),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'component' => 'Magento_Catalog/js/custom-options-type',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'selectType' => 'group',
                        'dataScope' => 'magenest_reservation_reservation_option_need_staff',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->getReservationOption(),
                        'disableLabel' => true,
                        'multiple' => false,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getReservationGridOption0($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Schedule'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => 'magenest_reservation_is_delete',
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'dataScope' => 'magenest_reservation_grid_option_0',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => 'magenest_reservation_sort_order',
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        'magenest_reservation_day' => $this->getDayConfig(10),
                        'magenest_reservation_from_hour' => $this->getHourConfig('From Time (Hour)', 20),
                        'magenest_reservation_from_min' => $this->getMinConfig('From Time (Min)', 30),
                        'magenest_reservation_to_hour' => $this->getHourConfig('To Time (Hour)', 40),
                        'magenest_reservation_to_min' => $this->getMinConfig('To Time (Min)', 50),
                        'magenest_reservation_slot' => $this->getSlotConfig(60),
                        'magenest_reservation_delete' => $this->getDeleteConfig(70)
                    ]
                ]
            ]
        ];
    }

    protected function getReservationGridOption1($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Schedule'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => 'magenest_reservation_is_delete',
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'dataScope' => 'magenest_reservation_grid_option_1',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => 'magenest_reservation_sort_order',
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        'magenest_reservation_day' => $this->getDayConfig(10),
                        'magenest_reservation_slot' => $this->getSlotConfig(20),
                        'magenest_reservation_delete' => $this->getDeleteConfig(30)
                    ]
                ]
            ]
        ];
    }

    protected function getStaffOption()
    {
        return [
            [
                'label' => 'No',
                'value' => 'no'
            ],
            [
                'label' => 'Yes',
                'value' => 'yes'
            ]
        ];
    }

    protected function getReservationOption()
    {
        return [
            [
                'label' => 'Some hours in 1 day',
                'value' => 'reservationOption0'
            ],
            [
                'label' => 'Full day',
                'value' => 'reservationOption1'
            ]
        ];
    }

    protected function getDayConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Day'),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'selectType' => 'group',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => [
                            [
                                'label' => 'Monday',
                                'value' => '1'
                            ],
                            [
                                'label' => 'Tuesday',
                                'value' => '2'
                            ],
                            [
                                'label' => 'Wednesday',
                                'value' => '3'
                            ],
                            [
                                'label' => 'Thursday',
                                'value' => '4'
                            ],
                            [
                                'label' => 'Friday',
                                'value' => '5'
                            ],
                            [
                                'label' => 'Saturday',
                                'value' => '6'
                            ],
                            [
                                'label' => 'Sunday',
                                'value' => '7'
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    protected function getDeleteConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => ActionDelete::NAME,
                        'fit' => true,
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
    }

    protected function getHourConfig($label, $sortOrder)
    {
        $options = [];
        for ($i = 0; $i <= 23; $i++) {
            $j = strval($i);
            if (strlen($j) < 2) {
                $j = '0' . $j;
            }
            $options[] = [
                'label' => $j,
                'value' => $j
            ];
        }
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'selectType' => 'group',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $options,
                    ]
                ]
            ]
        ];
    }

    protected function getMinConfig($label, $sortOrder)
    {
        $options = [];
        for ($i = 0; $i <= 59; $i++) {
            $j = strval($i);
            if (strlen($j) < 2) {
                $j = '0' . $j;
            }
            $options[] = [
                'label' => $j,
                'value' => $j
            ];
        }
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'selectType' => 'group',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $options,
                    ]
                ]
            ]
        ];
    }

    protected function getSlotConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Slot'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => 'magenest_reservation_slot',
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }
}
