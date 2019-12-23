<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:12
 */
namespace Magenest\Reservation\Model\ResourceModel\Special;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\Special
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\Special', 'Magenest\Reservation\Model\ResourceModel\Special');
    }
}
