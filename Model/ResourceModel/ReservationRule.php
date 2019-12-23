<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:54
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class ReservationRule
 * @package Magenest\Reservation\Model\ResourceModel
 */
class ReservationRule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_product_price_rule', 'id');
    }
}
