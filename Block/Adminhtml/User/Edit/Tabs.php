<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:46
 */
namespace Magenest\Reservation\Block\Adminhtml\User\Edit;

/**
 * Class Tabs
 * @package Magenest\Reservation\Block\Adminhtml\User\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('User Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'schedule',
            [
                'label' => __('Schedule'),
                'title' => __('Schedule'),
                'active' => true
            ]
        );

        $this->addTab(
            'info',
            [
                'label' => __('Information'),
                'title' => __('Information'),
                'active' => true
            ]
        );

        $this->addTab(
            'reservation',
            [
                'label' => __('ProductSchedule'),
                'title' => __('ProductSchedule'),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}
