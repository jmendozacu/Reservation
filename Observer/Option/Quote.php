<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:22
 */
namespace Magenest\Reservation\Observer\Option;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Quote
 * @package Magenest\Reservation\Observer\Option
 */
class Quote implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magenest\Reservation\Model\ProductSchedule
     */
    protected $_productSchedule;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory
     */
    protected $_productScheduleWithoutStaff;

    /**
     * @var \Magento\Checkout\Helper\Cart $_cartHelper
     */
    protected $_cartHelper;
    protected $_logger;

    /**
     * Quote constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory
    ) {
        $this->_productScheduleWithoutStaff = $productScheduleWithoutStaffFactory;
        $this->_logger = $logger;
        $this->_cartHelper = $cartHelper;
        $this->_objectManager = $objectManagerInterface;
        $this->_productSchedule = $productScheduleFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        $errorMessage = "The other customer just purchases a service in your cart.";
        /** @var  $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getEvent()->getQuote();

        /** @var  $quoteItem \Magento\Quote\Model\Quote\Item */
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
                /** @var  $orderItem \Magento\Sales\Model\Order\Item */
                $orderItem = $order->getItemByQuoteItemId($quoteItem->getId());
                $options = $orderItem->getProductOptions();
                $additionalOptionsList = json_decode($additionalOptions->getValue(),true);
                if (sizeof($additionalOptionsList) > 1) {
                    if ($additionalOptionsList[0]['label'] == 'Reservation Option') {
                        $orderAdded = 0;
                        if ($additionalOptionsList[0]['value'] == 'Full day without staff') {
                            for ($i = 1; $i < sizeof($additionalOptionsList); $i++) {
                                $newOrder = [];
                                $newOrder[] = $additionalOptionsList[$i]['label'];
                                $slotNumArray = explode('|', $additionalOptionsList[$i]['value']);
                                $slotNum = $slotNumArray[1];
                                $slotNum = str_replace(' slot(s)', '', $slotNum);
                                $newOrder[] = $slotNum;
                                $strExploded = explode("/", $additionalOptionsList[$i]['label']);
                                if ($strExploded[0] < 10) {
                                    $strExploded[0] = '0' . $strExploded[0];
                                }
                                if ($strExploded[1] < 10) {
                                    $strExploded[1] = '0' . $strExploded[1];
                                }
                                $newDateNum = date_create($strExploded[2] . '-' . $strExploded[1] . '-' . $strExploded[0]);
                                $productOrders = $this->_productScheduleWithoutStaff->create()->getCollection()->
                                addFieldToFilter('product_id', $orderItem->getProductId())->addFieldToFilter('weekday', date_format($newDateNum, "N"))->getFirstItem();
                                if ($productOrders->getId()) {
                                    if (strlen($productOrders->getOrders()) > 1) {
                                        $oldOrders = json_decode($productOrders->getOrders(),true);
                                        foreach ($oldOrders as $oldOrdersItem) {
                                            if ($oldOrdersItem[0] == $newOrder[0] && $oldOrdersItem[1] == $productOrders->getSlots()) {
                                                $orderAdded = 1;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($orderAdded == 1) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __($errorMessage)
                                );
                            }
                        } elseif ($additionalOptionsList[0]['value'] == 'Some hours in 1 day without staff') {
                            $orderAdded = 0;
                            $date = strtotime($additionalOptionsList[1]['value']);
                            for ($i = 2; $i < sizeof($additionalOptionsList); $i++) {
                                $fromToString = $additionalOptionsList[$i]['label'];
                                $fromToString = str_replace('From ', '', $fromToString);
                                $fromToString = str_replace(' To ', '|', $fromToString);
                                $fromToArray = explode('|', $fromToString);
                                $thisProductSchedule = $this->_productScheduleWithoutStaff->create()->getCollection()
                                    ->addFieldToFilter('product_id', $orderItem->getProductId())->addFieldToFilter('weekday', date('N', $date))
                                    ->addFieldToFilter('from_time', $fromToArray[0])->addFieldToFilter('to_time', $fromToArray[1])->getFirstItem();
                                if (strlen($thisProductSchedule->getOrders()) > 1) {
                                    $oldOrders = json_decode($thisProductSchedule->getOrders(),true);
                                    foreach ($oldOrders as $oldOrdersItem) {
                                        if ($oldOrdersItem[0] == date('Y/m/d', $date) && $oldOrdersItem[1] == $thisProductSchedule->getSlots()) {
                                            $orderAdded = 1;
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($orderAdded == 1) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __($errorMessage)
                                );
                            }
                        } elseif ($additionalOptionsList[0]['value'] == 'Some hours in 1 day with staff') {
                            $orderAdded = 0;
                            $date = strtotime($additionalOptionsList[1]['value']);
                            for ($i = 2; $i < sizeof($additionalOptionsList); $i++) {
                                $fromToString = $additionalOptionsList[$i]['label'];
                                $fromToString = str_replace('From ', '', $fromToString);
                                $fromToString = str_replace(' To ', '|', $fromToString);
                                $fromToArray = explode('|', $fromToString);
                                $thisProductSchedule = $this->_productScheduleWithoutStaff->create()->getCollection()
                                    ->addFieldToFilter('product_id', $orderItem->getProductId())->addFieldToFilter('weekday', date('N', $date))
                                    ->addFieldToFilter('from_time', $fromToArray[0])->addFieldToFilter('to_time', $fromToArray[1])->getFirstItem();
                                if (strlen($thisProductSchedule->getOrders()) > 1) {
                                    $oldOrders = json_decode($thisProductSchedule->getOrders(),true);
                                    foreach ($oldOrders as $oldOrdersItem) {
                                        if ($oldOrdersItem == date('Y/m/d', $date)) {
                                            $orderAdded = 1;
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($orderAdded == 1) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __($errorMessage)
                                );
                            }
                        } elseif ($additionalOptionsList[0]['value'] == 'Full day with staff') {
                            for ($i = 1; $i < sizeof($additionalOptionsList); $i++) {
                                $data = explode('|', $additionalOptionsList[$i]['value']);
                                $productOrders = $this->_productSchedule->create()->getCollection()
                                    ->addFieldToFilter('product_id', $orderItem->getProductId())
                                    ->addFieldToFilter('staff_id', $data[1])
                                    ->addFieldToFilter('weekday', date('N', strtotime($additionalOptionsList[$i]['label'])))
                                    ->getFirstItem();
                                if ($productOrders->getId()) {
                                    if (strlen($productOrders->getOrders()) > 1) {
                                        $oldOrders = json_decode($productOrders->getOrders(),true);
                                        foreach ($oldOrders as $oldOrdersItem) {
                                            if ($oldOrdersItem == date('Y/m/d', strtotime($additionalOptionsList[$i]['label']))) {
                                                $orderAdded = 1;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($orderAdded == 1) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __($errorMessage)
                                );
                            }
                        }
                    }
                    $options ['additional_options'] = $additionalOptionsList;
                    $orderItem->setProductOptions($options);
                }
            }
        }
    }
}
