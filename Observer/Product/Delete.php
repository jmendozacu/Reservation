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
 * Class Delete
 * @package Magenest\Reservation\Observer\Product
 */
class Delete implements ObserverInterface
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
     * @var \Magenest\Reservation\Model\StaffScheduleFactory
     */
    protected $_staffScheduleFactory;

    /**
     * @var \Magenest\Reservation\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Delete constructor.
     * @param \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory
     * @param \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory
     * @param \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory
     * @param \Magenest\Reservation\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleWithoutStaffFactory $productScheduleWithoutStaffFactory,
        \Magenest\Reservation\Model\ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_productFactory = $productFactory;
        $this->_productScheduleWithoutStaffFactory = $productScheduleWithoutStaffFactory;
        $this->_staffScheduleFactory = $staffScheduleFactory;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->_request->getParams();
        $productId = null;
        if (isset($params['selected'])) {
            $productIds = $params['selected'];
            foreach ($productIds as $productId) {
                $staffScheduleModel = $this->_staffScheduleFactory->create();
                $productScheduleModel = $this->_productScheduleFactory->create();
                $productModel = $this->_productFactory->create();
                $productScheduleWithoutStaffModel = $this->_productScheduleWithoutStaffFactory->create();

                $oldStaffSchedule = $staffScheduleModel->getCollection()->addFieldToFilter('product_id', $productId);
                if ($oldStaffSchedule) {
                    foreach ($oldStaffSchedule as $oldStaffScheduleItem) {
                        $oldStaffScheduleItem->delete();
                    }
                }
                $oldProductSchedule = $productScheduleModel->getCollection()->addFieldToFilter('product_id', $productId);
                if ($oldProductSchedule) {
                    foreach ($oldProductSchedule as $oldProductScheduleItem) {
                        $oldProductScheduleItem->delete();
                    }
                }
                $oldProduct = $productModel->getCollection()->addFieldToFilter('product_id', $productId);
                if ($oldProduct) {
                    foreach ($oldProduct as $oldProductItem) {
                        $oldProductItem->delete();
                    }
                }
                $oldProductScheduleWithoutStaff = $productScheduleWithoutStaffModel->getCollection()->addFieldToFilter('product_id', $productId);
                if ($oldProductScheduleWithoutStaff) {
                    foreach ($oldProductScheduleWithoutStaff as $oldProductScheduleWithoutStaffItem) {
                        $oldProductScheduleWithoutStaffItem->delete();
                    }
                }
            }
        }
    }
}
