<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 03:11
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class Order
 * @package Magenest\Reservation\Model\ResourceModel
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_order', 'id');
    }
}
