<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:55
 */
namespace Magenest\Reservation\Model\ResourceModel\Staff;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\Staff
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\Staff', 'Magenest\Reservation\Model\ResourceModel\Staff');
    }
}
