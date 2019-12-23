<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 03:12
 */
namespace Magenest\Reservation\Model\ResourceModel\Order;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\Order
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\Order', 'Magenest\Reservation\Model\ResourceModel\Order');
    }
}
