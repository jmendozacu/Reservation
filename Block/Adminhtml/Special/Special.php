<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:17
 */
namespace Magenest\Reservation\Block\Adminhtml\Special;

/**
 * Class Special
 * @package Magenest\Reservation\Block\Adminhtml\Special
 */
class Special extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_Reservation';

        parent::_construct();
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Magenest\Reservation\Block\Adminhtml\Special\Grid',
                'reservation.special.grid'
            )
        );
        return parent::_prepareLayout();
    }
}
