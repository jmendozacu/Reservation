<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 03:50
 */
namespace Magenest\Reservation\Controller\Adminhtml\Order;

use Magenest\Reservation\Model\OrderFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class MassStatus
 * @package Magenest\Reservation\Controller\Adminhtml\Order
 */
class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Reservation\Model\Email\Mail
     */
    protected $_mail;

    /**
     * MassStatus constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param OrderFactory $orderFactory
     * @param Registry $registry
     * @param LoggerInterface $loggerInterface
     * @param Filter $filter
     */

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
    protected $_coreOrderFactory;

    /**
     * @var OrderFactory
     */
    protected $_reservationOrder;

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * MassStatus constructor.
     * @param \Magenest\Reservation\Model\Email\Mail $mail
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param OrderFactory $orderFactory
     * @param \Magenest\Reservation\Model\ProductFactory $reservationProductFactory
     * @param \Magento\Sales\Model\OrderFactory $coreOrderFactory
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory
     * @param Registry $registry
     * @param LoggerInterface $loggerInterface
     * @param Filter $filter
     */
    public function __construct(
        \Magenest\Reservation\Model\Email\Mail $mail,
        Action\Context $context,
        PageFactory $pageFactory,
        OrderFactory $orderFactory,
        \Magenest\Reservation\Model\ProductFactory $reservationProductFactory,
        \Magento\Sales\Model\OrderFactory $coreOrderFactory,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        Registry $registry,
        LoggerInterface $loggerInterface,
        Filter $filter
    ) {
        $this->_coreOrderFactory = $coreOrderFactory;
        $this->_reservationProductFactory = $reservationProductFactory;
        $this->_reservationOrder = $orderFactory;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_productScheduleWithoutStaffFactory = $productScheduleWithoutStaffFactory;
        $this->_mail = $mail;
        $this->_filter = $filter;
        parent::__construct($context);
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderCollection = $this->_reservationOrder->create()->getCollection();
        $collections = $this->_filter->getCollection($orderCollection);
        $reservationProductModel = $this->_reservationProductFactory->create();
        $reservationSchedule = $this->_productScheduleFactory->create();
        $reservationScheduleWithoutStaff = $this->_productScheduleWithoutStaffFactory->create();
        $status = (int)$this->getRequest()->getParam('status');
        $totals = 0;
        switch ($status) {
            case 0:
                try {
                    foreach ($collections as $item) {
                        if ($item->getReservationStatus() != 'confirmed') {
                            $reservationOrderModel = $this->_reservationOrder->create();
                            $thisOrder = $reservationOrderModel->load($item->getId());
                            $thisOrder->setData('reservation_status', 'confirmed')->save();
                            $totals++;
                            $this->_mail->sendMailToStaff('confirmed', $item->getId());
                            $this->_mail->sendMailToCustomer('confirmed', $item->getId());
                        }
                    }
                    $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                break;
            case 1:
                try {
                    foreach ($collections as $item) {
                        if ($item->getReservationStatus() != 'unconfirmed') {
                            $reservationOrderModel = $this->_reservationOrder->create();
                            $thisOrder = $reservationOrderModel->load($item->getId());
                            $thisOrder->setData('reservation_status', 'unconfirmed')->save();
                            $totals++;
                            $this->_mail->sendMailToStaff('unconfirmed', $item->getId());
                            $this->_mail->sendMailToCustomer('unconfirmed', $item->getId());
                        }
                    }
                    $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                break;
            case 2:
                try {
                    foreach ($collections as $item) {
                        if ($item->getReservationStatus() != 'canceled') {
                            $orderModel = $this->_coreOrderFactory->create();
                            $reservationOrderModel = $this->_reservationOrder->create();
                            $totals++;
                            $thisOrder = $reservationOrderModel->load($item->getId());
                            $thisOrder->setData('status', 'canceled');
                            $thisOrder->setData('reservation_status', 'canceled')->save();
                            $thisCoreOrder = $orderModel->load($item->getOrderId());
                            $thisCoreOrder->setState('canceled');
                            $thisCoreOrder->setStatus('canceled')->save();
                            $thisProduct = $reservationProductModel
                                ->getCollection()
                                ->addFieldToFilter('product_id', $thisOrder->getOrderItemId())
                                ->getFirstItem();
                            $weekDay = date('N', strtotime($thisOrder->getDate()));
                            if ($thisProduct->getNeedStaff() == 0 && $thisProduct->getOption() == 0) {
                                $oldOrder = $reservationScheduleWithoutStaff
                                    ->getCollection()
                                    ->addFieldToFilter('product_id', $thisOrder->getOrderItemId())
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
                            } else {
                                if ($thisProduct->getNeedStaff() == 0 && $thisProduct->getOption() == 1) {
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
                                } else {
                                    if ($thisProduct->getNeedStaff() == 1 && $thisProduct->getOption() == 0) {
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
                                    } else {
                                        if ($thisProduct->getNeedStaff() == 1 && $thisProduct->getOption() == 1) {
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
                                    }
                                }
                            }
                        }
                    }
                    $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                break;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::orders');
    }
}
