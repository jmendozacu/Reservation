<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 23/07/2016
 * Time: 13:00
 */
namespace Magenest\Reservation\Model\ResourceModel\User;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\User
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\User', 'Magenest\Reservation\Model\ResourceModel\User');
    }
}
