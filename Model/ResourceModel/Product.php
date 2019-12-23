<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 11/07/2016
 * Time: 11:35
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class Product
 * @package Magenest\Reservation\Model\ResourceModel
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_product', 'id');
    }
}
