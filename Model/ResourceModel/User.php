<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 23/07/2016
 * Time: 12:59
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class User
 * @package Magenest\Reservation\Model\ResourceModel
 */
class User extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_user', 'id');
    }
}
