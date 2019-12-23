<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:11
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class Special
 * @package Magenest\Reservation\Model\ResourceModel
 */
class Special extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_special_date', 'id');
    }
}
