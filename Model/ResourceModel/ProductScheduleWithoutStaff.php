<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:41
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class ProductScheduleWithoutStaff
 * @package Magenest\Reservation\Model\ResourceModel
 */
class ProductScheduleWithoutStaff extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_product_schedule_without_staff', 'id');
    }
}
