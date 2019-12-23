<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:11
 */
namespace Magenest\Reservation\Block\Adminhtml\Cancel;

/**
 * Class Cancel
 * @package Magenest\Reservation\Block\Adminhtml\Cancel
 */
class Cancel extends \Magento\Backend\Block\Widget\Grid\Container
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
            $this->getLayout()->createBlock(
                'Magenest\Reservation\Block\Adminhtml\Cancel\Grid',
                'reservation.cancel.grid'
            )
        );
        return parent::_prepareLayout();
    }
}
