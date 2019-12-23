<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 03:03
 */
namespace Magenest\Reservation\Block\Adminhtml\Order;

/**
 * Class Order
 * @package Magenest\Reservation\Block\Adminhtml\Order
 */
class Order extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_Reservation';

        parent::_construct();
        $this->removeButton('add');
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magenest\Reservation\Block\Adminhtml\Order\Grid', 'reservation.order.grid')
        );
        return parent::_prepareLayout();
    }
}
