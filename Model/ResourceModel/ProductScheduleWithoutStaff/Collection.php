<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 11/07/2016
 * Time: 13:18
 */
namespace Magenest\Reservation\Model\ResourceModel\ProductScheduleWithoutStaff;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\ProductScheduleWithoutStaff
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\ProductScheduleWithoutStaff', 'Magenest\Reservation\Model\ResourceModel\ProductScheduleWithoutStaff');
    }
}
