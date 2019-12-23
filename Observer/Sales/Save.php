<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 04/07/2016
 * Time: 16:58
 */
namespace Magenest\Reservation\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Save
 * @package Magenest\ProductSchedule\Observer\Sales
 */
class Save implements ObserverInterface
{
    protected $_logger;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magenest\Reservation\Model\Email\Mail
     */
    protected $_mail;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_reservationOrder;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magenest\Reservation\Model\Product
     */
    protected $_product;

    /**
     * @var \Magenest\Reservation\Model\ProductSchedule
     */
    protected $_productSchedule;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory
     */
    protected $_productScheduleWithoutStaff;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * Save constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magenest\Reservation\Model\Email\Mail $mail
     * @param \Magenest\Reservation\Model\ProductFactory $productFactory
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magenest\Reservation\Model\OrderFactory $reservationOrderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Model\Currency $currency
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Reservation\Model\Email\Mail $mail,
        \Magenest\Reservation\Model\ProductFactory $productFactory,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magenest\Reservation\Model\OrderFactory $reservationOrderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\Currency $currency
    ) {
        $this->_mail = $mail;
        $this->_order = $orderFactory;
        $this->_reservationOrder = $reservationOrderFactory;
        $this->_logger = $logger;
        $this->_product = $productFactory;
        $this->_productSchedule = $productScheduleFactory;
        $this->_productScheduleWithoutStaff = $productScheduleWithoutStaffFactory;
        $this->_request = $request;
        $this->_filesystem = $filesystem;
        $this->_messageManager = $messageManager;
        $this->_orderFactory = $orderFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_currency = $currency;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $order_item */
        $order_item = $observer->getEvent()->getItem();
        /**
         * @var \Magenest\Reservation\Model\OrderFactory $isHaveCancelItem
         */
        $isHaveCancelItem = $this->_reservationOrder->create()->getCollection()->addFieldToFilter('order_id', $order_item->getOrderId())
            ->getData();
        $isCancelRequest = 0;
        foreach ($isHaveCancelItem as $cancelItem) {
            if ($cancelItem['status'] == 'canceled' || $cancelItem['reservation_status'] == 'canceled'){
                $isCancelRequest = 1;
                break;
            }
        }
        if ($order_item && $isCancelRequest == 0) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $order_item->getProduct();
            $product_id = $product->getId();
            $product_name = $product->getName();

            /** Get order information */
            $order_id = $order_item->getOrderId();

            /** get additional options */
            $option = $order_item->getProductOptions();

            /** Check if this is reservation product  */
            $reservationProduct = 0;
            $thisProduct = $this->_product->create()->getCollection()->addFieldToFilter('product_id', $product_id)->getFirstItem();
            if ($thisProduct->getId()) {
                $reservationProduct = 1;
            }
            if ($reservationProduct == 1) {
                $canceled = 1;
                if ($order_item->getId()) {
                    if ($order_item->getStatusId() == \Magento\Sales\Model\Order\Item::STATUS_INVOICED) {
                        $canceled = 0;
                    }
                }
                if ($canceled == 0) {
                    $additional_options = $option['additional_options'];
                    $order_object = $order_item->getOrder();
                    $customer_name = $order_object->getCustomerName();
                    $customer_id = $order_object->getCustomerId();
                    $customer_email = $order_object->getCustomerEmail();
                    $reservationOrder = $this->_reservationOrder->create();
                    $default_status = $this->_scopeConfig->getValue(
                        'magenest_order_config/config/default_status',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    if ($default_status == null) {
                        $default_status = 'confirmed';
                    }
                    if ($additional_options[0]['value'] == 'Full day without staff') {
                        for ($i = 1; $i < sizeof($additional_options); $i++) {
                            $slotNumArray = explode('|', $additional_options[$i]['value']);
                            $slotNum = $slotNumArray[1];
                            $slotNum = str_replace(' slot(s)', '', $slotNum);
                            $newOrder[] = $slotNum;
                            $additional_options[$i]['label'];
                            $strExploded = explode("/", $additional_options[$i]['label']);
                            if ($strExploded[0] < 10) {
                                $strExploded[0] = '0' . $strExploded[0];
                            }
                            if ($strExploded[1] < 10) {
                                $strExploded[1] = '0' . $strExploded[1];
                            }
                            $newDateNum = date_create($strExploded[2] . '-' . $strExploded[1] . '-' . $strExploded[0]);
                            $productOrders = $this->_productScheduleWithoutStaff->create()->getCollection()->
                            addFieldToFilter('product_id', $product_id)->addFieldToFilter('weekday', date_format($newDateNum, "N"))->getFirstItem();
                            if ($productOrders->getId()) {
                                if (strlen($productOrders->getOrders()) > 1) {
                                    $oldOrders = unserialize($productOrders->getOrders());
                                    $finalOrder = [];
                                    $orderAdded = 0;
                                    foreach ($oldOrders as $oldOrdersItem) {
                                        if ($oldOrdersItem[0] == $additional_options[$i]['label']) {
                                            $oldOrdersItem[1] += $slotNum;
                                            if (strcmp($oldOrdersItem[0], date('Y/m/d')) > 0) {
                                                $finalOrder[] = $oldOrdersItem;
                                            }
                                            $orderAdded = 1;
                                        } else {
                                            $finalOrder[] = $oldOrdersItem;
                                        }
                                    }
                                    if ($orderAdded == 0) {
                                        $finalOrder[] = [$additional_options[$i]['label'], $slotNum];
                                    }

                                    $productOrders->setData('orders', serialize($finalOrder))->save();
                                } else {
                                    $finalOrder = [];
                                    $finalOrder[] = [$additional_options[$i]['label'], $slotNum];
                                    $productOrders->setData('orders', serialize($finalOrder))->save();
                                }
                            }
                            $reservationOrder->setData([
                                'order_id' => $order_id,
                                'order_item_id' => $product_id,
                                'order_item_name' => $product_name,
                                'customer_id' => $customer_id,
                                'customer_email' => $customer_email,
                                'customer_name' => $customer_name,
                                'status' => $order_item->getStatus(),
                                'reservation_status' => $default_status,
                                'date' => date_format($newDateNum, "Y/m/d"),
                                'special_date' => $slotNumArray[0],
                                'from_time' => '00:00',
                                'to_time' => '23:59',
                                'slots' => $slotNum,
                                'user_name' => 'No Staff'
                            ])->save();
                            $this->_mail->sendMailToCustomer($default_status, $reservationOrder->getId());
                        }
                    } elseif ($additional_options[0]['value'] == 'Some hours in 1 day without staff') {
                        $date = strtotime($additional_options[1]['value']);
                        for ($i = 2; $i < sizeof($additional_options); $i++) {
                            $data = explode('|', $additional_options[$i]['value']);
                            $slotNum = $data[1];
                            $slotNum = str_replace(' slot(s)', '', $slotNum);
                            $fromToString = $additional_options[$i]['label'];
                            $fromToString = str_replace('From ', '', $fromToString);
                            $fromToString = str_replace(' To ', '|', $fromToString);
                            $fromToArray = explode('|', $fromToString);
                            $thisProductSchedule = $this->_productScheduleWithoutStaff->create()->getCollection()
                                ->addFieldToFilter('product_id', $product_id)->addFieldToFilter('weekday', date('N', $date))
                                ->addFieldToFilter('from_time', $fromToArray[0])->addFieldToFilter('to_time', $fromToArray[1])->getFirstItem();
                            if (strlen($thisProductSchedule->getOrders()) > 1) {
                                $oldOrders = unserialize($thisProductSchedule->getOrders());
                                $finalOrder = [];
                                $orderAdded = 0;
                                foreach ($oldOrders as $oldOrdersItem) {
                                    if ($oldOrdersItem[0] == date('Y/m/d', $date)) {
                                        $oldOrdersItem[1] += $slotNum;
                                        if (strcmp($oldOrdersItem[0], date('Y/m/d')) > 0) {
                                            $finalOrder[] = $oldOrdersItem;
                                        }
                                        $orderAdded = 1;
                                    } else {
                                        $finalOrder[] = $oldOrdersItem;
                                    }
                                }
                                if ($orderAdded == 0) {
                                    $finalOrder[] = [date('Y/m/d', $date), $slotNum];
                                }
                                $thisProductSchedule->setData('orders', serialize($finalOrder))->save();
                            } else {
                                $finalOrder = [];
                                $finalOrder[] = [date('Y/m/d', $date), $slotNum];
                                $thisProductSchedule->setData('orders', serialize($finalOrder))->save();
                            }
                            $reservationOrder->setData([
                                'order_id' => $order_id,
                                'order_item_id' => $product_id,
                                'order_item_name' => $product_name,
                                'from_time' => $fromToArray[0],
                                'to_time' => $fromToArray[1],
                                'customer_id' => $customer_id,
                                'customer_email' => $customer_email,
                                'customer_name' => $customer_name,
                                'reservation_status' => $default_status,
                                'date' => date('Y/m/d', $date),
                                'special_date' => $data[0],
                                'status' => $order_item->getStatus(),
                                'slots' => $slotNum,
                                'user_name' => 'No Staff'
                            ])->save();
                            $this->_mail->sendMailToCustomer($default_status, $reservationOrder->getId());
                        }
                    } elseif ($additional_options[0]['value'] == 'Some hours in 1 day with staff') {
                        $date = strtotime($additional_options[1]['value']);
                        for ($i = 2; $i < sizeof($additional_options); $i++) {
                            $fromToString = $additional_options[$i]['label'];
                            $fromToString = str_replace('From ', '', $fromToString);
                            $fromToString = str_replace(' To ', '|', $fromToString);
                            $fromToArray = explode('|', $fromToString);
                            $data = explode('|', $additional_options[$i]['value']);
                            $thisProductSchedule = $this->_productSchedule->create()->getCollection()
                                ->addFieldToFilter('product_id', $product_id)->addFieldToFilter('staff_id', $data[1])->addFieldToFilter('weekday', date('N', $date))
                                ->addFieldToFilter('from_time', $fromToArray[0])->addFieldToFilter('to_time', $fromToArray[1])->getFirstItem();
                            $finalOrder = [];
                            if (strlen($thisProductSchedule->getOrders()) > 1) {
                                $oldOrders = unserialize($thisProductSchedule->getOrders());
                                foreach ($oldOrders as $oldOrdersItem) {
                                    if (strcmp($oldOrdersItem, date('Y/m/d')) > 0) {
                                        $finalOrder[] = $oldOrdersItem;
                                    }
                                }
                                $finalOrder[] = date('Y/m/d', $date);
                                $thisProductSchedule->setData('orders', serialize($finalOrder))->save();
                            } else {
                                $finalOrder[] = date('Y/m/d', $date);
                                $thisProductSchedule->setData('orders', serialize($finalOrder))->save();
                            }
                            $reservationOrder->setData([
                                'order_id' => $order_id,
                                'order_item_id' => $product_id,
                                'order_item_name' => $product_name,
                                'customer_id' => $customer_id,
                                'customer_email' => $customer_email,
                                'customer_name' => $customer_name,
                                'reservation_status' => $default_status,
                                'date' => date('Y/m/d', $date),
                                'special_date' => $data[0],
                                'from_time' => $fromToArray[0],
                                'to_time' => $fromToArray[1],
                                'user_id' => $data[1],
                                'user_name' => $data[2],
                                'status' => $order_item->getStatus(),
                                'slots' => 1
                            ])->save();
                            $thisOrderId = $reservationOrder->getCollection()
                                ->addFieldToFilter('order_id', $order_id)
                                ->addFieldToFilter('order_item_id', $product_id)
                                ->addFieldToFilter('customer_id', $customer_id)
                                ->addFieldToFilter('user_id', $data[1])
                                ->addFieldToFilter('date', date('Y/m/d', $date))
                                ->addFieldToFilter('from_time', $fromToArray[0])
                                ->addFieldToFilter('to_time', $fromToArray[1])
                                ->getFirstItem()->getId();
                            $this->_mail->sendMailToStaff($default_status, $thisOrderId);
                            $this->_mail->sendMailToCustomer($default_status, $thisOrderId);
                        }
                    } elseif ($additional_options[0]['value'] == 'Full day with staff') {
                        for ($i = 1; $i < sizeof($additional_options); $i++) {
                            $data = explode('|', $additional_options[$i]['value']);
                            $productOrders = $this->_productSchedule->create()->getCollection()
                                ->addFieldToFilter('product_id', $product_id)
                                ->addFieldToFilter('staff_id', $data[1])
                                ->addFieldToFilter('weekday', date('N', strtotime($additional_options[$i]['label'])))
                                ->getFirstItem();
                            if (strlen($productOrders->getOrders()) > 1) {
                                $oldOrders = unserialize($productOrders->getOrders());
                                $oldOrders[] = date('Y/m/d', strtotime($additional_options[$i]['label']));
                                $finalOrder = [];
                                foreach ($oldOrders as $oldOrdersItem) {
                                    if (strcmp($oldOrdersItem, date('Y/m/d')) > 0) {
                                        $finalOrder[] = $oldOrdersItem;
                                    }
                                }
                                $productOrders->setData('orders', serialize($finalOrder))->save();
                            } else {
                                $finalOrder = [];
                                $finalOrder[] = date('Y/m/d', strtotime($additional_options[$i]['label']));
                                $productOrders->setData('orders', serialize($finalOrder))->save();
                            }
                            $reservationOrder->setData([
                                'order_id' => $order_id,
                                'order_item_id' => $product_id,
                                'order_item_name' => $product_name,
                                'customer_id' => $customer_id,
                                'customer_email' => $customer_email,
                                'customer_name' => $customer_name,
                                'reservation_status' => $default_status,
                                'date' => date('Y/m/d', strtotime($additional_options[$i]['label'])),
                                'special_date' => $data[0],
                                'user_id' => $data[1],
                                'user_name' => $data[2],
                                'status' => $order_item->getStatus(),
                                'from_time' => '00:00',
                                'to_time' => '23:59',
                                'slots' => 1
                            ])->save();
                            $thisOrderId = $reservationOrder->getCollection()
                                ->addFieldToFilter('order_id', $order_id)
                                ->addFieldToFilter('order_item_id', $product_id)
                                ->addFieldToFilter('customer_id', $customer_id)
                                ->addFieldToFilter('user_id', $data[1])
                                ->addFieldToFilter('date', date('Y/m/d', strtotime($additional_options[$i]['label'])))
                                ->getFirstItem()->getId();
                            $this->_mail->sendMailToCustomer($default_status, $thisOrderId);
                            $this->_mail->sendMailToStaff($default_status, $thisOrderId);
                        }
                    }
                }
            }
        }
    }
}
