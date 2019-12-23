<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:24
 */
namespace Magenest\Reservation\Observer\Product;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Save
 * @package Magenest\Reservation\Observer\Product
 */
class Save implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magenest\Reservation\Model\ProductSchedule
     */
    protected $_productScheduleFactory;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory
     */
    protected $_productScheduleWithoutStaffFactory;

    /**
     * @var \Magenest\Reservation\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    protected $_logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        \Magenest\Reservation\Model\ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_logger = $logger;
        $this->_messageManager = $messageManager;
        $this->_productFactory = $productFactory;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_productScheduleWithoutStaffFactory = $productScheduleWithoutStaffFactory;
        $this->_request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->_request->getParams();
        $thisProduct = $params['product'];
        $thisProductId = 0;
        if (array_key_exists('id', $params)) {
            $thisProductId = $params['id'];
        } else {
            $thisProductId = $observer->getEvent()->getProduct()->getId();
        }
        if ($thisProduct['magenest_reservation_type'] == 1) {
            $needToCheckChange = 0;
            $needToCheckScheduleChange = 0;
            $thisProductStaff = 0;
            $thisProductReservationOption = 0;
            $reservationProductFactory = $this->_productFactory->create();
            $reservationProductScheduleWithoutStaffFactory = $this->_productScheduleWithoutStaffFactory->create();
            $oldReservationProduct = $reservationProductFactory->getCollection()->addFieldToFilter('product_id', $thisProductId)->getFirstItem();

            if ($oldReservationProduct->getId()) {
                $needToCheckChange = 1;
            }
            if ($needToCheckChange == 1) {
                if (array_key_exists('magenest_reservation_staff_option', $thisProduct)) {
                    $thisProductStaff = $thisProduct['magenest_reservation_staff_option'];
                    if ($thisProductStaff == 'no') {
                        $thisProductStaff = 0;
                    } else {
                        $thisProductStaff = 1;
                    }
                }
                if ($thisProductStaff == 0 && array_key_exists('magenest_reservation_reservation_option_no_staff', $thisProduct)) {
                    $thisProductReservationOption = $thisProduct['magenest_reservation_reservation_option_no_staff'];
                    if ($thisProductReservationOption == 'reservationOption0') {
                        $thisProductReservationOption = 0;
                    } else {
                        $thisProductReservationOption = 1;
                    }
                } elseif ($thisProductStaff == 1 && array_key_exists('magenest_reservation_reservation_option_need_staff', $thisProduct)) {
                    $thisProductReservationOption = $thisProduct['magenest_reservation_reservation_option_need_staff'];
                    if ($thisProductReservationOption == 'reservationOption0') {
                        $thisProductReservationOption = 0;
                    } else {
                        $thisProductReservationOption = 1;
                    }
                }
                if ($thisProductStaff == $oldReservationProduct->getNeedStaff() && $thisProductReservationOption == $oldReservationProduct->getOption()) {
                    $needToCheckScheduleChange = 1;
                }
            }
            /**
             * schedule changed, if no staff needed and some hours in 1 day
             */
            if ($needToCheckScheduleChange == 1 && $thisProductStaff == 0 && $thisProductReservationOption == 0) {
                $oldReservationScheduleProduct = $reservationProductScheduleWithoutStaffFactory
                    ->getCollection()
                    ->addFieldToFilter('product_id', $thisProductId);
                if (array_key_exists('magenest_reservation_grid_option_0', $thisProduct)) {
                    $newReservationSchedule = $thisProduct['magenest_reservation_grid_option_0']['magenest_reservation_grid_option_0'];
                    foreach ($oldReservationScheduleProduct as $oldReservationScheduleProductItem) {
                        $temp = 0;
                        foreach ($newReservationSchedule as $newReservationScheduleItem) {
                            if ($newReservationScheduleItem['magenest_reservation_day'] == $oldReservationScheduleProductItem->getWeekday() &&
                                $newReservationScheduleItem['magenest_reservation_from_hour'] . ':' . $newReservationScheduleItem['magenest_reservation_from_min'] == $oldReservationScheduleProductItem->getFromTime() &&
                                $newReservationScheduleItem['magenest_reservation_to_hour'] . ':' . $newReservationScheduleItem['magenest_reservation_to_min'] == $oldReservationScheduleProductItem->getToTime()
                            ) {
                                $temp = 1;
                            }
                        }
                        if ($temp == 0) {
                            $oldReservationScheduleProductItem->delete();
                        }
                    }
                    foreach ($newReservationSchedule as $newReservationScheduleItem) {
                        $temp = 0;
                        foreach ($oldReservationScheduleProduct as $oldReservationScheduleProductItem) {
                            if ($newReservationScheduleItem['magenest_reservation_day'] == $oldReservationScheduleProductItem->getWeekday() &&
                                $newReservationScheduleItem['magenest_reservation_from_hour'] . ':' . $newReservationScheduleItem['magenest_reservation_from_min'] == $oldReservationScheduleProductItem->getFromTime() &&
                                $newReservationScheduleItem['magenest_reservation_to_hour'] . ':' . $newReservationScheduleItem['magenest_reservation_to_min'] == $oldReservationScheduleProductItem->getToTime()
                            ) {
                                $temp = 1;
                                $oldReservationScheduleProductItem->setData('slots', $newReservationScheduleItem['magenest_reservation_slot'])->save();
                            }
                        }
                        if ($temp == 0) {
                            $reservationProductScheduleWithoutStaffFactory->setData([
                                'product_id' => $thisProductId,
                                'weekday' => $newReservationScheduleItem['magenest_reservation_day'],
                                'from_time' => $newReservationScheduleItem['magenest_reservation_from_hour'] . ':' . $newReservationScheduleItem['magenest_reservation_from_min'],
                                'to_time' => $newReservationScheduleItem['magenest_reservation_to_hour'] . ':' . $newReservationScheduleItem['magenest_reservation_to_min'],
                                'slots' => $newReservationScheduleItem['magenest_reservation_slot']
                            ])->save();
                        }
                    }
                } else {
                    foreach ($oldReservationScheduleProduct as $oldReservationScheduleProductItem) {
                        $oldReservationScheduleProductItem->delete();
                    }
                }
            }
            /**
             * schedule changed, if no staff needed and full day option
             */
            if ($needToCheckScheduleChange == 1 && $thisProductStaff == 0 && $thisProductReservationOption == 1) {
                $oldReservationScheduleProduct = $reservationProductScheduleWithoutStaffFactory
                    ->getCollection()
                    ->addFieldToFilter('product_id', $thisProductId);
                if (array_key_exists('magenest_reservation_grid_option_1', $thisProduct)) {
                    $newReservationSchedule = $thisProduct['magenest_reservation_grid_option_1']['magenest_reservation_grid_option_1'];
                    foreach ($oldReservationScheduleProduct as $oldReservationScheduleProductItem) {
                        $temp = 0;
                        foreach ($newReservationSchedule as $newReservationScheduleItem) {
                            if ($newReservationScheduleItem['magenest_reservation_day'] == $oldReservationScheduleProductItem->getWeekday()
                                && !array_key_exists('magenest_reservation_is_delete', $newReservationScheduleItem)) {
                                $temp = 1;
                            }
                        }
                        if ($temp == 0) {
                            $oldReservationScheduleProductItem->delete();
                        }
                    }
                    foreach ($newReservationSchedule as $newReservationScheduleItem) {
                        if (!array_key_exists('magenest_reservation_is_delete', $newReservationScheduleItem)) {
                            $temp = 0;
                            foreach ($oldReservationScheduleProduct as $oldReservationScheduleProductItem) {
                                if ($newReservationScheduleItem['magenest_reservation_day'] == $oldReservationScheduleProductItem->getWeekday()) {
                                    $temp = 1;
                                    $oldReservationScheduleProductItem->setData('slots', $newReservationScheduleItem['magenest_reservation_slot'])->save();
                                }
                            }
                            if ($temp == 0) {
                                $reservationProductScheduleWithoutStaffFactory->setData([
                                    'product_id' => $thisProductId,
                                    'weekday' => $newReservationScheduleItem['magenest_reservation_day'],
                                    'slots' => $newReservationScheduleItem['magenest_reservation_slot']
                                ])->save();
                            }
                        }
                    }
                } else {
                    foreach ($oldReservationScheduleProduct as $oldReservationScheduleProductItem) {
                        $oldReservationScheduleProductItem->delete();
                    }
                }
            }
            if ($needToCheckScheduleChange == 1 && $thisProductStaff == 1) {
                $oldReservationProduct->setData('need_staff', $thisProductStaff)->save();
                $oldReservationProduct->setData('option', $thisProductReservationOption)->save();
            }
            if ($needToCheckScheduleChange == 0) {
                $oldReservationProduct = $reservationProductFactory->getCollection()->addFieldToFilter('product_id', $thisProductId);
                if ($oldReservationProduct) {
                    foreach ($oldReservationProduct as $oldReservationProductItem) {
                        $oldReservationProductItem->delete();
                    }
                }
                $oldReservationProduct = $this->_productScheduleFactory->create()->getCollection()->addFieldToFilter('product_id', $thisProductId);
                if ($oldReservationProduct) {
                    foreach ($oldReservationProduct as $oldReservationProductItem) {
                        $oldReservationProductItem->delete();
                    }
                }
                $oldReservationProduct = $reservationProductScheduleWithoutStaffFactory->getCollection()->addFieldToFilter('product_id', $thisProductId);
                if ($oldReservationProduct) {
                    foreach ($oldReservationProduct as $oldReservationProductItem) {
                        $oldReservationProductItem->delete();
                    }
                }
                if (array_key_exists('magenest_reservation_staff_option', $thisProduct)) {
                    $thisProductStaff = $thisProduct['magenest_reservation_staff_option'];
                    if ($thisProductStaff == 'no') {
                        $thisProductStaff = 0;
                    } else {
                        $thisProductStaff = 1;
                    }
                    /**
                     * if this product needs staff, we save staff and reservation option
                     */
                    if ($thisProductStaff == 1 && array_key_exists('magenest_reservation_reservation_option_need_staff', $thisProduct)) {
                        $thisProductReservationOption = $thisProduct['magenest_reservation_reservation_option_need_staff'];
                        if ($thisProductReservationOption == 'reservationOption0') {
                            $thisProductReservationOption = 0;
                        } else {
                            $thisProductReservationOption = 1;
                        }
                        /**
                         * save it to database
                         */
                        $reservationProductFactory->setData([
                            'product_id' => $thisProductId,
                            'need_staff' => 1,
                            'option' => $thisProductReservationOption
                        ])->save();
                    }
                    /**
                     * if this product does not need staff, we reservation option, schedule
                     */
                    if ($thisProductStaff == 0 && array_key_exists('magenest_reservation_reservation_option_no_staff', $thisProduct)) {
                        $thisProductReservationOption = $thisProduct['magenest_reservation_reservation_option_no_staff'];
                        if ($thisProductReservationOption == 'reservationOption0') {
                            $thisProductReservationOption = 0;
                        } else {
                            $thisProductReservationOption = 1;
                        }
                        /**
                         * save product to database
                         */
                        $reservationProductFactory->setData([
                            'product_id' => $thisProductId,
                            'need_staff' => 0,
                            'option' => $thisProductReservationOption
                        ])->save();
                        /**
                         * if this this product has option some hours in 1 day, we save its schedule to database
                         */
                        if ($thisProductReservationOption == 0 && array_key_exists('magenest_reservation_grid_option_0', $thisProduct)) {
                            $newReservationSchedule = $thisProduct['magenest_reservation_grid_option_0']['magenest_reservation_grid_option_0'];
                            $newReservationScheduleFinal = [];
                            foreach ($newReservationSchedule as $newReservationScheduleItem) {
                                $scheduleAdded = 0;
                                if (sizeof($newReservationScheduleFinal) > 0) {
                                    foreach ($newReservationScheduleFinal as $newReservationScheduleFinalItem) {
                                        if ($newReservationScheduleItem['magenest_reservation_day'] == $newReservationScheduleFinalItem['magenest_reservation_day'] &&
                                            $newReservationScheduleItem['magenest_reservation_from_hour'] == $newReservationScheduleFinalItem['magenest_reservation_from_hour'] &&
                                            $newReservationScheduleItem['magenest_reservation_from_min'] == $newReservationScheduleFinalItem['magenest_reservation_from_min'] &&
                                            $newReservationScheduleItem['magenest_reservation_to_hour'] == $newReservationScheduleFinalItem['magenest_reservation_to_hour'] &&
                                            $newReservationScheduleItem['magenest_reservation_to_min'] == $newReservationScheduleFinalItem['magenest_reservation_to_min'] &&
                                            $newReservationScheduleItem['magenest_reservation_slot'] == $newReservationScheduleFinalItem['magenest_reservation_slot']
                                        ) {
                                            $scheduleAdded = 1;
                                            break;
                                        }
                                        if ($newReservationScheduleItem['magenest_reservation_day'] == $newReservationScheduleFinalItem['magenest_reservation_day']) {
                                            if (strcmp(
                                                $newReservationScheduleItem['magenest_reservation_from_hour'] . $newReservationScheduleItem['magenest_reservation_from_min'],
                                                $newReservationScheduleFinalItem['magenest_reservation_from_hour'] . $newReservationScheduleFinalItem['magenest_reservation_from_min']
                                            ) >= 0 && strcmp(
                                                $newReservationScheduleItem['magenest_reservation_from_hour'] . $newReservationScheduleItem['magenest_reservation_from_min'],
                                                $newReservationScheduleFinalItem['magenest_reservation_to_hour'] . $newReservationScheduleFinalItem['magenest_reservation_to_min']
                                            ) < 0
                                            ) {
                                                $scheduleAdded = 1;
                                                break;
                                            }
                                            if (strcmp(
                                                $newReservationScheduleItem['magenest_reservation_to_hour'] . $newReservationScheduleItem['magenest_reservation_to_min'],
                                                $newReservationScheduleFinalItem['magenest_reservation_from_hour'] . $newReservationScheduleFinalItem['magenest_reservation_from_min']
                                            ) > 0 && strcmp(
                                                $newReservationScheduleItem['magenest_reservation_to_hour'] . $newReservationScheduleItem['magenest_reservation_to_min'],
                                                $newReservationScheduleFinalItem['magenest_reservation_to_hour'] . $newReservationScheduleFinalItem['magenest_reservation_to_min']
                                            ) <= 0
                                            ) {
                                                $scheduleAdded = 1;
                                                break;
                                            }
                                        }
                                    }
                                }
                                if (strcmp(
                                    $newReservationScheduleItem['magenest_reservation_from_hour'] . $newReservationScheduleItem['magenest_reservation_from_min'],
                                    $newReservationScheduleItem['magenest_reservation_to_hour'] . $newReservationScheduleItem['magenest_reservation_to_min']
                                ) >= 0
                                ) {
                                    $scheduleAdded = 1;
                                }
                                if ($newReservationScheduleItem['magenest_reservation_slot'] <= 0) {
                                    $scheduleAdded = 1;
                                }
                                if ($scheduleAdded == 0) {
                                    $newReservationScheduleFinal[] = $newReservationScheduleItem;
                                } else {
                                    $this->_messageManager->addErrorMessage(__('Something went wrong in Magenest Reservation Configuration, please check it again'));
                                }
                            }
                            /**
                             * remove all old schedule
                             */
                            $oldSchedule = $reservationProductScheduleWithoutStaffFactory->getCollection()->addFieldToFilter('product_id', $thisProductId);
                            if ($oldSchedule) {
                                foreach ($oldSchedule as $oldScheduleItem) {
                                    $oldScheduleItem->delete();
                                }
                            }
                            /**
                             * save all new schedule
                             */
                            foreach ($newReservationScheduleFinal as $newReservationScheduleFinalItem) {
                                $reservationProductScheduleWithoutStaffFactory->setData([
                                    'product_id' => $thisProductId,
                                    'weekday' => $newReservationScheduleFinalItem['magenest_reservation_day'],
                                    'from_time' => $newReservationScheduleFinalItem['magenest_reservation_from_hour'] . ':' . $newReservationScheduleFinalItem['magenest_reservation_from_min'],
                                    'to_time' => $newReservationScheduleFinalItem['magenest_reservation_to_hour'] . ':' . $newReservationScheduleFinalItem['magenest_reservation_to_min'],
                                    'slots' => $newReservationScheduleFinalItem['magenest_reservation_slot']
                                ])->save();
                            }
                        }

                        /**
                         * if this this product has option full day, we save its schedule to database
                         */
                        if ($thisProductReservationOption == 1 && array_key_exists('magenest_reservation_grid_option_1', $thisProduct)) {
                            $newReservationSchedule = $thisProduct['magenest_reservation_grid_option_1']['magenest_reservation_grid_option_1'];
                            $newReservationScheduleFinal = [];
                            foreach ($newReservationSchedule as $newReservationScheduleItem) {
                                $scheduleAdded = 0;
                                if (sizeof($newReservationScheduleFinal) > 0) {
                                    foreach ($newReservationScheduleFinal as $newReservationScheduleFinalItem) {
                                        if ($newReservationScheduleItem['magenest_reservation_day'] == $newReservationScheduleFinalItem['magenest_reservation_day'] &&
                                            $newReservationScheduleItem['magenest_reservation_slot'] == $newReservationScheduleFinalItem['magenest_reservation_slot']) {
                                            $scheduleAdded = 1;
                                            break;
                                        }
                                    }
                                }
                                if ($scheduleAdded == 0) {
                                    $newReservationScheduleFinal[] = $newReservationScheduleItem;
                                }
                            }
                            /**
                             * remove all old schedule
                             */
                            $oldSchedule = $reservationProductScheduleWithoutStaffFactory->getCollection()->addFieldToFilter('product_id', $thisProductId);
                            if ($oldSchedule) {
                                foreach ($oldSchedule as $oldScheduleItem) {
                                    $oldScheduleItem->delete();
                                }
                            }
                            /**
                             * save all new schedule
                             */

                            $weekDayTemp = [];
                            foreach ($newReservationScheduleFinal as $newReservationScheduleFinalItem) {
                                $scheduleAdded = 0;
                                if (sizeof($weekDayTemp) > 0) {
                                    foreach ($weekDayTemp as $weekDayTempItem) {
                                        if ($weekDayTempItem == $newReservationScheduleFinalItem['magenest_reservation_day']) {
                                            $scheduleAdded = 1;
                                        }
                                    }
                                }
                                if ($scheduleAdded == 0) {
                                    $weekDayTemp[] = $newReservationScheduleFinalItem['magenest_reservation_day'];
                                    $reservationProductScheduleWithoutStaffFactory->setData([
                                        'product_id' => $thisProductId,
                                        'weekday' => $newReservationScheduleFinalItem['magenest_reservation_day'],
                                        'slots' => $newReservationScheduleFinalItem['magenest_reservation_slot']
                                    ])->save();
                                } else {
                                    $this->_messageManager->addErrorMessage(__('Double schedule found in Magenest Reservation Configuration, please check this product schedule again'));
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $oldReservationProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $thisProductId)->getFirstItem();
            if ($oldReservationProduct->getId()) {
                $oldReservationProduct->delete();
            }
        }
    }
}
