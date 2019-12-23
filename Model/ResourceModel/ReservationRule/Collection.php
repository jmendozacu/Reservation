<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:55
 */
namespace Magenest\Reservation\Model\ResourceModel\ReservationRule;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\ReservationRule
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\ReservationRule', 'Magenest\Reservation\Model\ResourceModel\ReservationRule');
    }
}
