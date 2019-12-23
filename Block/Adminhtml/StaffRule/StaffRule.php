<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:28
 */
namespace Magenest\Reservation\Block\Adminhtml\StaffRule;

/**
 * Class StaffRule
 * @package Magenest\Reservation\Block\Adminhtml\StaffRule
 */
class StaffRule extends \Magento\Backend\Block\Widget\Grid\Container
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
            $this->getLayout()->createBlock('Magenest\Reservation\Block\Adminhtml\StaffRule\Grid', 'reservation.rule.grid')
        );
        return parent::_prepareLayout();
    }
}
