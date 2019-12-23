<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:29
 */
namespace Magenest\Reservation\Observer\Staff;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Delete
 * @package Magenest\Reservation\Observer\Staff
 */
class Delete implements ObserverInterface
{

    /**
     * @var \Magenest\Reservation\Model\StaffFactory
     */
    protected $_staffFactory;

    /**
     * @var \Magenest\Reservation\Model\StaffScheduleFactory
     */
    protected $_staffScheduleFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productScheduleFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Delete constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magenest\Reservation\Model\StaffFactory $staffFactory
     * @param \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magenest\Reservation\Model\StaffFactory $staffFactory,
        \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_request = $request;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_staffFactory = $staffFactory;
        $this->_staffScheduleFactory = $staffScheduleFactory;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->_request->getParams();
        $userId = null;
        if (isset($params['user_id'])) {
            $userId = $params['user_id'];
            if ($userId) {
                $staffModel = $this->_staffFactory->create();
                $staffScheduleModel = $this->_staffScheduleFactory->create();
                $productModel = $this->_productScheduleFactory->create();
                $oldStaff = $staffModel->getCollection()->addFieldToFilter('staff_id', $userId)->getFirstItem();
                if ($oldStaff) {
                    $oldStaff->delete();
                }
                $oldStaffSchedule = $staffScheduleModel->getCollection()->addFieldToFilter('staff_id', $userId);
                if ($oldStaffSchedule) {
                    foreach ($oldStaffSchedule as $oldStaffScheduleItem) {
                        $oldStaffScheduleItem->delete();
                    }
                }
                $oldProductSchedule = $productModel->getCollection()->addFieldToFilter('staff_id', $userId);
                if ($oldProductSchedule) {
                    foreach ($oldProductSchedule as $oldProductScheduleItem) {
                        $oldProductScheduleItem->delete();
                    }
                }
            }
        }
    }
}
