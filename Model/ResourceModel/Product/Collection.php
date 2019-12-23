<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 11/07/2016
 * Time: 11:36
 */
namespace Magenest\Reservation\Model\ResourceModel\Product;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\Product
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\Product', 'Magenest\Reservation\Model\ResourceModel\Product');
    }
}
