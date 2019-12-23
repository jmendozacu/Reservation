<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:13
 */
namespace Magenest\Reservation\Model\ResourceModel;

/**
 * Class Cancel
 * @package Magenest\Reservation\Model\ResourceModel
 */
class Cancel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_reservation_cancel_request', 'id');
    }
}
