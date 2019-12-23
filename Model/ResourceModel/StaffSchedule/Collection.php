<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:54
 */
namespace Magenest\Reservation\Model\ResourceModel\StaffSchedule;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\StaffSchedule
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\StaffSchedule', 'Magenest\Reservation\Model\ResourceModel\StaffSchedule');
    }
}
