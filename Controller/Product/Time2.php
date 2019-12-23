<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 13/07/2016
 * Time: 10:34
 */
namespace Magenest\Reservation\Controller\Product;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 *
 */
class Time2 extends \Magento\Framework\App\Action\Action
{
    protected $_logger;

    /**
     * @var \Magenest\Reservation\Model\ProductFactory
     */
    protected $_reservationProductFactory;

    /**
     * @var \Magenest\Reservation\Model\SpecialFactory
     */
    protected $_specialFactory;

    /**
     * @var \Magenest\Reservation\Model\ReservationRuleFactory
     */
    protected $_reservationRuleFactory;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory
     */
    protected $_productScheduleWithoutStaffFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Reservation\Model\ProductFactory $reservationProductFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        \Magenest\Reservation\Model\SpecialFactory $specialFactory,
        \Magenest\Reservation\Model\ReservationRuleFactory $reservationRuleFactory,
        \Magento\Directory\Helper\Data $currency
    ) {
        $this->_logger = $logger;
        $this->_reservationProductFactory = $reservationProductFactory;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        $this->_specialFactory = $specialFactory;
        $this->_reservationRuleFactory = $reservationRuleFactory;
        $this->_productScheduleWithoutStaffFactory = $productScheduleWithoutStaffFactory;
        $this->_currency = $currency;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $productId = $params['product_id'];
        $date = $params['date'];
        $productPrice = $params['product_price'];
        $schedules = $this->_productScheduleWithoutStaffFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('product_id', $productId);
        $reservationRuleModel = $this->_reservationRuleFactory->create();
        $specialDateModel = $this->_specialFactory->create()->getCollection();
        $strExploded = explode(",", $date);
        $day = $strExploded[0];
        $dayFilter = 0;
        if ($day == 'Mon') {
            $dayFilter = 1;
        }
        if ($day == 'Tue') {
            $dayFilter = 2;
        }
        if ($day == 'Wed') {
            $dayFilter = 3;
        }
        if ($day == 'Thu') {
            $dayFilter = 4;
        }
        if ($day == 'Fri') {
            $dayFilter = 5;
        }
        if ($day == 'Sat') {
            $dayFilter = 6;
        }
        if ($day == 'Sun') {
            $dayFilter = 7;
        }
        $newDateNum = strtotime($strExploded[1]);
        $newDate = date('Y/m/d', $newDateNum);
        $today = date('Y/m/d');
        if (strcmp($newDate, $today) <= 0) {
            return null;
        }
        $scheduleAfterFilters = [];
        foreach ($schedules as $schedule) {
            $oldOrders = unserialize($schedule->getOrders());
            $orderAdded = 0;
            $orderAvailableNum = $schedule->getSlots();
            if ($oldOrders) {
                foreach ($oldOrders as $oldOrder) {
                    if ($oldOrder[0] == $newDate && $schedule->getWeekday() == $dayFilter && $oldOrder[1] == $schedule->getSlots()) {
                        $orderAdded = 1;
                        break;
                    } elseif ($oldOrder[0] == $newDate && $schedule->getWeekday() == $dayFilter && $oldOrder[1] < $schedule->getSlots()) {
                        $orderAvailableNum -= $oldOrder[1];
                    }
                }
            }
            if ($orderAdded == 0) {
                if ($schedule->getWeekday() == $dayFilter) {
                    $specialAmount = 0;
                    $specialName = __("No Special Event");
                    $specialAdded = 0;
                    if ($specialDateModel) {
                        foreach ($specialDateModel as $specialDate) {
                            $fromTemp = date('Y/m/d H:i:s', strtotime($specialDate->getDateFrom()));
                            $toTemp = date('Y/m/d H:i:s', strtotime($specialDate->getDateTo()));
                            if (strcmp($newDate . ' ' . $schedule->getFromTime() . ':00', $fromTemp) >= 0 && strcmp($newDate . ' ' . $schedule->getToTime() . ':00', $toTemp) <= 0) {
                                if ($specialDate->getDateOption() == "1") {
                                    $specialAmount = $specialDate->getDateAmount();
                                } else {
                                    $specialAmount -= $specialDate->getDateAmount();
                                }
                                if ($specialAdded == 0) {
                                    $specialName = $specialDate->getDateName();
                                    $specialAdded = 1;
                                } else {
                                    $specialName .= ', ' . $specialDate->getDateName();
                                }
                            }
                        }
                    }
                    /**
                     * add reservation rule
                     */
                    $reservationRuleAdded = 0;
                    $earlyOrder = 0;
                    $reservationRulePrice = $productPrice;
                    $reservationRulePrice += $specialAmount;
                    $reservationRuleCollection = $reservationRuleModel->getCollection();
                    if ($reservationRuleCollection) {
                        $reservationRuleData = $reservationRuleCollection->getData();
                        usort(
                            $reservationRuleData,
                            function ($item1, $item2) {
                                if ($item1['rule_unit'] == $item2['rule_unit']) {
                                    return 0;
                                }
                                return ($item1['rule_unit'] > $item2['rule_unit']) ? 1 : -1;
                            }
                        );
                        foreach ($reservationRuleData as $reservationRuleDataItem) {
                            $option = $reservationRuleDataItem['rule_option'];
                            switch ($option) {
                                case 1:
                                    $from = $reservationRuleDataItem['rule_from'];
                                    $to = $reservationRuleDataItem['rule_to'];
                                    if (strcmp($schedule->getFromTime() . ':00', $from) >= 0 && strcmp($schedule->getToTime() . ':00', $to) <= 0) {
                                        if ($reservationRuleAdded == 0) {
                                            $reservationRuleAdded = 1;
                                            $specialName .= __(', Rush hour');
                                        }
                                        if ($reservationRuleDataItem['rule_function'] == 1) {
                                            $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                            $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                            $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                        } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                            $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                        }
                                    }
                                    break;
                                case 2:
                                    $from = $reservationRuleDataItem['rule_from'];
                                    $to = $reservationRuleDataItem['rule_to'];
                                    $fromArray = explode(",", $from);
                                    $toArray = explode(",", $to);
                                    $fromDay = $fromArray[0];
                                    if ($fromDay == 'Mon') {
                                        $fromDay = 1;
                                    } elseif ($fromDay == 'Tue') {
                                        $fromDay = 2;
                                    } elseif ($fromDay == 'Wed') {
                                        $fromDay = 3;
                                    } elseif ($fromDay == 'Thu') {
                                        $fromDay = 4;
                                    } elseif ($fromDay == 'Fri') {
                                        $fromDay = 5;
                                    } elseif ($fromDay == 'Sat') {
                                        $fromDay = 6;
                                    } elseif ($fromDay == 'Sun') {
                                        $fromDay = 7;
                                    }
                                    $toDay = $toArray[0];
                                    if ($toDay == 'Mon') {
                                        $toDay = 1;
                                    } elseif ($toDay == 'Tue') {
                                        $toDay = 2;
                                    } elseif ($toDay == 'Wed') {
                                        $toDay = 3;
                                    } elseif ($toDay == 'Thu') {
                                        $toDay = 4;
                                    } elseif ($toDay == 'Fri') {
                                        $toDay = 5;
                                    } elseif ($toDay == 'Sat') {
                                        $toDay = 6;
                                    } elseif ($toDay == 'Sun') {
                                        $toDay = 7;
                                    }
                                    if ($dayFilter >= $fromDay && $dayFilter <= $toDay) {
                                        if (strcmp($schedule->getFromTime() . ":00", $fromArray[1]) >= 0 && strcmp($schedule->getToTime() . ":00", $toArray[1]) <= 0) {
                                            if ($reservationRuleAdded == 0) {
                                                $reservationRuleAdded = 1;
                                                $specialName .= __(', Rush hour');
                                            }
                                            if ($reservationRuleDataItem['rule_function'] == 1) {
                                                $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                            } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                                $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                            } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                                $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                            } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                                $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                            }
                                        }
                                    }
                                    break;
                                case 3:
                                    $from = $reservationRuleDataItem['rule_from'];
                                    $to = $reservationRuleDataItem['rule_to'];
                                    $fromArray = explode(",", $from);
                                    $toArray = explode(",", $to);
                                    $fromDay = $fromArray[0];
                                    $toDay = $toArray[0];
                                    $newDay = date('d', $newDateNum);
                                    if ($newDay > $fromDay && $newDay < $toDay) {
                                        if ($reservationRuleAdded == 0) {
                                            $reservationRuleAdded = 1;
                                            $specialName .= __(', Rush hour');
                                        }
                                        if ($reservationRuleDataItem['rule_function'] == 1) {
                                            $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                            $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                            $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                        } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                            $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                        }
                                    } elseif ($newDay == $fromDay) {
                                        if (strcmp($schedule->getFromTime() . ":00", $fromArray[1]) >= 0) {
                                            if ($reservationRuleAdded == 0) {
                                                $reservationRuleAdded = 1;
                                                $specialName .= __(', Rush hour');
                                            }
                                            if ($reservationRuleDataItem['rule_function'] == 1) {
                                                $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                            } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                                $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                            } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                                $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                            } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                                $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                            }
                                        }
                                    } elseif ($newDay == $toDay) {
                                        if (strcmp($schedule->getToTime() . ":00", $fromArray[1]) <= 0) {
                                            if ($reservationRuleAdded == 0) {
                                                $reservationRuleAdded = 1;
                                                $specialName .= __(', Rush hour');
                                            }
                                            if ($reservationRuleDataItem['rule_function'] == 1) {
                                                $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                            } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                                $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                            } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                                $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                            } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                                $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                            }
                                        }
                                    }
                                    break;
                                case 4:
                                    $from = $reservationRuleDataItem['rule_from'];
                                    $to = $reservationRuleDataItem['rule_to'];
                                    $fromDate = date('m/d H:i:s', strtotime($from));
                                    $toDate = date('m/d H:i:s', strtotime($to));
                                    if (strcmp(date('m/d', $newDateNum) . ' ' . $schedule->getFromTime() . ":00", $fromDate) >= 0
                                        && strcmp(date('m/d', $newDateNum) . ' ' . $schedule->getToTime() . ":00", $toDate) <= 0
                                    ) {
                                        if ($reservationRuleAdded == 0) {
                                            $reservationRuleAdded = 1;
                                            $specialName .= __(', Rush hour');
                                        }
                                        if ($reservationRuleDataItem['rule_function'] == 1) {
                                            $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                            $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                            $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                        } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                            $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                        }
                                    }
                                    break;
                                case 5:
                                    /**
                                     * calculate interval time between this order to the schedule
                                     */
                                    $diff = date_diff(date_create(date('Y/m/d')), date_create($newDate));
                                    $diffInt = intval($diff->format("%a"));
                                    if ($diffInt > 0 && $diffInt >= $reservationRuleDataItem['rule_from']) {
                                        if ($earlyOrder == 0) {
                                            $specialName .= __(', Pre-order');
                                            $earlyOrder = 1;
                                        }
                                        if ($reservationRuleDataItem['rule_function'] == 1) {
                                            $reservationRulePrice += $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 2) {
                                            $reservationRulePrice -= $reservationRuleDataItem['rule_amount'];
                                        } elseif ($reservationRuleDataItem['rule_function'] == 3) {
                                            $reservationRulePrice = $reservationRulePrice * (100 + $reservationRuleDataItem['rule_amount']) / 100;
                                        } elseif ($reservationRuleDataItem['rule_function'] == 4) {
                                            $reservationRulePrice = $reservationRulePrice * (100 - $reservationRuleDataItem['rule_amount']) / 100;
                                        }
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                    if (intval($reservationRulePrice) <= 0) {
                        $reservationRulePrice = 0;
                    }
                    $reservationRulePrice = number_format((float)$reservationRulePrice, 2, '.', '');
                    array_push(
                        $scheduleAfterFilters,
                        [
                            'from_time' => $schedule->getFromTime(),
                            'to_time' => $schedule->getToTime(),
                            'slots' => $orderAvailableNum,
                            'event_name' => $specialName,
                            'event_amount' => $reservationRulePrice,
                            'symbol' => $this->_storeManager->getStore()->getCurrentCurrencyCode()
                        ]
                    );
                }
            }
        }
        usort(
            $scheduleAfterFilters,
            function ($item1, $item2) {
                if ($item1['from_time'] == $item2['from_time']) {
                    return 0;
                }
                return ($item1['from_time'] > $item2['from_time']) ? 1 : -1;
            }
        );
        $resultArray = json_encode($scheduleAfterFilters);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($resultArray);
        return $resultJson;
    }
}
