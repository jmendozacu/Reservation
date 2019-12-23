<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:42
 */
namespace Magenest\Reservation\Model\ResourceModel\ProductSchedule;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\ProductSchedule
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\ProductSchedule', 'Magenest\Reservation\Model\ResourceModel\ProductSchedule');
    }
}
