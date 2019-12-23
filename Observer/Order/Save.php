<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:27
 */
namespace Magenest\Reservation\Observer\Order;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Save
 * @package Magenest\Reservation\Observer\Order
 */
class Save implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_orderFactory;

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
     * @var \Magenest\Reservation\Model\ProductSchedule
     */
    protected $_productSchedule;

    /**
     * Save constructor.
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magenest\Reservation\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Model\Currency $currency
     */
    public function __construct(
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magenest\Reservation\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\Currency $currency
    ) {
        $this->_productSchedule = $productScheduleFactory;
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
        $today = date('Y/m/d');
        /** @var \Magento\Quote\Model\Quote\Item $order_item */
        $order_item = $observer->getEvent()->getItem();
        if ($order_item) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $order_item->getProduct();
            $product_id = $product->getId();
            $product_name = $product->getName();

            /** Get order information */
            $order_id = $order_item->getOrderId();
            $status = $order_item->getStatus();

            /** get additional options */
            $option = $order_item->getProductOptions();
            if ($product->getTypeId() == 'reservation') {
                $saleOrder = $this->_orderFactory->create()->load($order_id);
                $canceled = 0;
                if ($saleOrder->getId()) {
                    if ($saleOrder->getState() == 'canceled') {
                        $canceled = 1;
                    }
                }
                if ($canceled == 0) {
                    $additional_options = $option['additional_options'];
                    $booked_date = $additional_options[0]['value'];
                    $weekday_date = strtotime($booked_date);
                    $weekday = date('w', $weekday_date);
                    if ($weekday == 0) {
                        $weekday = 7;
                    }
                    $default_status = $this->_scopeConfig->getValue(
                        'magenest_order_config/config/default_status',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $productModel = $this->_productSchedule->create();
                    /** Getting customer info */
                    $order_object = $order_item->getOrder();
                    $customer_name = $order_object->getCustomerName();
                    $customer_id = $order_object->getCustomerId();
                    $customer_email = $order_object->getCustomerEmail();
                    $model = $this->_reservationOrderFactory->create();
                    for ($i = 1; $i < sizeof($additional_options); $i++) {
                        $bookedTime1 = $additional_options[$i]['label'];
                        $bookedTime2 = str_replace("From ", "", $bookedTime1);
                        $bookedTime = str_replace(" To ", ",", $bookedTime2);
                        $bookArray = explode(',', $bookedTime);
                        $fromTime = $bookArray[0];
                        $toTime = $bookArray[1];
                        $staffArray = explode('|', $additional_options[$i]['value']);

                        $data = array(
                            'order_id' => $order_id,
                            'order_item_id' => $product_id,
                            'order_item_name' => $product_name,
                            'customer_id' => $customer_id,
                            'customer_email' => $customer_email,
                            'customer_name' => $customer_name,
                            'status' => $status,
                            'date' => $booked_date,
                            'special_date' => $staffArray[3],
                            'from_time' => $fromTime,
                            'to_time' => $toTime,
                            'user_id' => $staffArray[0],
                            'user_name' => $staffArray[1],
                            'reservation_status' => $default_status
                        );
                        /**
                         * check all order before save
                         */
                        $oldOrders = $model->getCollection()
                            ->addFieldToFilter('order_id', $order_id)
                            ->addFieldToFilter('order_item_id', $product_id)
                            ->addFieldToFilter('customer_id', $customer_id)
                            ->addFieldToFilter('date', $booked_date)
                            ->addFieldToFilter('from_time', $fromTime)
                            ->addFieldToFilter('to_time', $toTime)->getFirstItem();
                        if ($oldOrders->getId()) {
                        } else {
                            $model->setData($data)->save();
                        }
                        /**
                         * remove expired order to product schedule and insert this order to product schedule
                         */
                        $productOrders = $productModel->getCollection()->addFieldToFilter('product_id', $product_id)
                            ->addFieldToFilter('staff_id', $staffArray[0])->addFieldToFilter('weekday', $weekday)
                            ->addFieldToFilter('from_time', $fromTime)->addFieldToFilter('to_time', $toTime)
                            ->getFirstItem();
                        if ($productOrders->getId()) {
                            if ($productOrders->getOrders() != null && sizeof($productOrders->getOrders()) > 0) {
                                $productOldOrders = unserialize($productOrders->getOrders());
                                for ($j = 0; $j < sizeof($productOldOrders); $j++) {
                                    if (strcmp($productOldOrders[$j], $today) < 0) {
                                        $productOldOrders[$j] = '0';
                                    }
                                    if (strcmp($productOldOrders[$j], date('Y/m/d', $weekday_date)) == 0) {
                                        $productOldOrders[$j] = '0';
                                    }
                                }
                                $productNewOrders = [];
                                foreach ($productOldOrders as $productOldOrder) {
                                    if ($productOldOrder != '0') {
                                        array_push($productNewOrders, $productOldOrder);
                                    }
                                }
                                array_push($productNewOrders, date('Y/m/d', $weekday_date));
                                $productOrders->setData('orders', serialize($productNewOrders))->save();
                            } else {
                                $productNewOrders = [];
                                $productNewOrders[] = date('Y/m/d', $weekday_date);
                                $productOrders->setData('orders', serialize($productNewOrders))->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
