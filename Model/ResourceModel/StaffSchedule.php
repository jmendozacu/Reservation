<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:52
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class StaffSchedule
 * @package Magenest\Reservation\Model\ResourceModel
 */
class StaffSchedule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_staff_schedule', 'id');
    }
}
