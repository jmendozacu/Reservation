<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:21
 */
namespace Magenest\Reservation\Controller\Adminhtml\Cancel;

use Magenest\Reservation\Model\CancelFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassCancelSchedule
 * @package Magenest\Reservation\Controller\Adminhtml\Cancel
 */
class MassCancelSchedule extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    protected $_logger;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_reservationOrder;

    /**
     * @var \Magenest\Reservation\Model\Email\Mail
     */
    protected $_mail;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleFactory
     */
    protected $_productScheduleFactory;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory
     */
    protected $_productScheduleWithoutStaffFactory;

    /**
     * @var \Magenest\Reservation\Model\ProductFactory
     */
    protected $_reservationProductFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var CancelFactory
     */
    protected $_cancelFactory;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * MassCancelSchedule constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param \Magenest\Reservation\Model\ProductFactory $reservationProductFactory
     * @param \Magenest\Reservation\Model\OrderFactory $reservationOrderFactory
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory
     * @param \Magenest\Reservation\Model\Email\Mail $mail
     * @param CancelFactory $cancelFactory
     * @param Registry $registry
     * @param Filter $filter
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Psr\Log\LoggerInterface $logger,
        Action\Context $context,
        \Magenest\Reservation\Model\ProductFactory $reservationProductFactory,
        \Magenest\Reservation\Model\OrderFactory $reservationOrderFactory,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        \Magenest\Reservation\Model\Email\Mail $mail,
        CancelFactory $cancelFactory,
        Registry $registry,
        Filter $filter
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_logger = $logger;
        $this->_orderFactory = $orderFactory;
        $this->_reservationProductFactory = $reservationProductFactory;
        $this->_reservationOrder = $reservationOrderFactory;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_productScheduleWithoutStaffFactory = $productScheduleWithoutStaffFactory;
        $this->_mail = $mail;
        $this->_filter = $filter;
        $this->_cancelFactory = $cancelFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_cancelFactory->create()->getCollection());
        $deletedCancel = 0;
        $orderModel = $this->_orderFactory->create();
        $reservationProductModel = $this->_reservationProductFactory->create();
        $reservationOrderModel = $this->_reservationOrder->create();
        $reservationSchedule = $this->_productScheduleFactory->create();
        $reservationScheduleWithoutStaff = $this->_productScheduleWithoutStaffFactory->create();
        /** @var \Magenest\Reservation\Model\Cancel $item */
        if ($collection) {
            foreach ($collection as $item) {
                $deletedCancel++;
                $thisOrder = $reservationOrderModel
                    ->getCollection()
                    ->addFieldToFilter('order_id', $item->getOrderId())
                    ->addFieldToFilter('order_item_id', $item->getOrderItemId())
                    ->addFieldToFilter('date', $item->getDate())
                    ->addFieldToFilter('from_time', $item->getFromTime())
                    ->addFieldToFilter('to_time', $item->getToTime())
                    ->getFirstItem();
                $thisOrder->setData('status', 'canceled');
                $thisOrder->setData('reservation_status', 'canceled')->save();
                $thisCoreOrder = $orderModel->load($item->getOrderId());
                $thisCoreOrder->setState('canceled');
                $thisCoreOrder->setStatus('canceled')->save();
                $thisProduct = $reservationProductModel->getCollection()->addFieldToFilter('product_id', $thisOrder->getOrderItemId())->getFirstItem();
                $weekDay = date('N', strtotime($thisOrder->getDate()));
                if ($thisProduct->getNeedStaff() == 0 && $thisProduct->getOption() == 0) {
                    $oldOrder = $reservationScheduleWithoutStaff->getCollection()->addFieldToFilter('product_id', $thisOrder->getOrderItemId())
                        ->addFieldToFilter('from_time', $thisOrder->getFromTime())->addFieldToFilter('to_time', $thisOrder->getToTime())
                        ->addFieldToFilter('weekday', $weekDay)->getFirstItem();
                    $oldScheduleString = $oldOrder->getOrders();
                    $oldScheduleArray = unserialize($oldScheduleString);
                    $newScheduleArray = [];
                    if ($oldScheduleArray) {
                        foreach ($oldScheduleArray as $oldScheduleArrayItem) {
                            if ($oldScheduleArrayItem[0] == $thisOrder->getDate()) {
                                $oldScheduleArrayItem[1] = $oldScheduleArrayItem[1] - $thisOrder->getSlots();
                            }
                            $newScheduleArray[] = $oldScheduleArrayItem;
                        }
                    }
                    if (sizeof($newScheduleArray) > 0) {
                        $oldOrder->setData('orders', serialize($newScheduleArray))->save();
                    } else {
                        $oldOrder->setData('orders', "")->save();
                    }
                    $this->_mail->sendMailToCustomer('canceled', $item->getId());
                } elseif ($thisProduct->getNeedStaff() == 0 && $thisProduct->getOption() == 1) {
                    $oldOrder = $reservationScheduleWithoutStaff->getCollection()
                        ->addFieldToFilter('product_id', $thisOrder->getOrderItemId())
                        ->addFieldToFilter('weekday', $weekDay)->getFirstItem();
                    $oldScheduleString = $oldOrder->getOrders();
                    $oldScheduleArray = unserialize($oldScheduleString);
                    $newScheduleArray = [];
                    if ($oldScheduleArray) {
                        foreach ($oldScheduleArray as $oldScheduleArrayItem) {
                            if ($oldScheduleArrayItem[0] == $thisOrder->getDate()) {
                                $oldScheduleArrayItem[1] = $oldScheduleArrayItem[1] - $thisOrder->getSlots();
                            }
                            $newScheduleArray[] = $oldScheduleArrayItem;
                        }
                    }
                    if (sizeof($newScheduleArray) > 0) {
                        $oldOrder->setData('orders', serialize($newScheduleArray))->save();
                    } else {
                        $oldOrder->setData('orders', "")->save();
                    }
                    $this->_mail->sendMailToCustomer('canceled', $item->getId());
                } elseif ($thisProduct->getNeedStaff() == 1 && $thisProduct->getOption() == 0) {
                    $oldOrder = $reservationSchedule->getCollection()
                        ->addFieldToFilter('product_id', $thisOrder->getOrderItemId())
                        ->addFieldToFilter('weekday', $weekDay)
                        ->addFieldToFilter('from_time', $thisOrder->getFromTime())
                        ->addFieldToFilter('to_time', $thisOrder->getToTime())->getFirstItem();
                    $oldScheduleString = $oldOrder->getOrders();
                    $oldScheduleArray = unserialize($oldScheduleString);
                    $newScheduleArray = [];
                    if ($oldScheduleArray) {
                        foreach ($oldScheduleArray as $oldScheduleArrayItem) {
                            if ($oldScheduleArrayItem != $thisOrder->getDate()) {
                                $newScheduleArray[] = $oldScheduleArrayItem;
                            }
                        }
                    }
                    if (sizeof($newScheduleArray) > 0) {
                        $oldOrder->setData('orders', serialize($newScheduleArray))->save();
                    } else {
                        $oldOrder->setData('orders', "")->save();
                    }
                    $this->_mail->sendMailToCustomer('canceled', $item->getId());
                    $this->_mail->sendMailToStaff('canceled', $item->getId());
                } elseif ($thisProduct->getNeedStaff() == 1 && $thisProduct->getOption() == 1) {
                    $oldOrder = $reservationSchedule->getCollection()
                        ->addFieldToFilter('product_id', $thisOrder->getOrderItemId())
                        ->addFieldToFilter('weekday', $weekDay)->getFirstItem();
                    $oldScheduleString = $oldOrder->getOrders();
                    $oldScheduleArray = unserialize($oldScheduleString);
                    $newScheduleArray = [];
                    if ($oldScheduleArray) {
                        foreach ($oldScheduleArray as $oldScheduleArrayItem) {
                            if ($oldScheduleArrayItem != $thisOrder->getDate()) {
                                $newScheduleArray[] = $oldScheduleArrayItem;
                            }
                        }
                    }
                    if (sizeof($newScheduleArray) > 0) {
                        $oldOrder->setData('orders', serialize($newScheduleArray))->save();
                    } else {
                        $oldOrder->setData('orders', "")->save();
                    }
                    $this->_mail->sendMailToCustomer('canceled', $item->getId());
                    $this->_mail->sendMailToStaff('canceled', $item->getId());
                }
                $item->delete();
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 schedule(s) have been canceled.', $deletedCancel)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('reservation/*/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::cancel');
    }
}
