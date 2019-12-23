<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:26
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class StaffRule
 * @package Magenest\Reservation\Model\ResourceModel
 */
class StaffRule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_staff_price_rule', 'id');
    }
}
