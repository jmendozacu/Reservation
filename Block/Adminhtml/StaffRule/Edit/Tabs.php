<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:46
 */
namespace Magenest\Reservation\Block\Adminhtml\StaffRule\Edit;

/**
 * Class Tabs
 * @package Magenest\Reservation\Block\Adminhtml\StaffRule\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Staff Price Rule Configuration'));
    }
}
