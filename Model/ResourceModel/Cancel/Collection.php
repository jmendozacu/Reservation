<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:14
 */
namespace Magenest\Reservation\Model\ResourceModel\Cancel;

/**
 * Class Collection
 * @package Magenest\Reservation\Model\ResourceModel\Cancel
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\Reservation\Model\Cancel', 'Magenest\Reservation\Model\ResourceModel\Cancel');
    }
}
