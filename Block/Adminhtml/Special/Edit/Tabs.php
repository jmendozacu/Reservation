<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:20
 */
namespace Magenest\Reservation\Block\Adminhtml\Special\Edit;

/**
 * Class Tabs
 * @package Magenest\Reservation\Block\Adminhtml\Special\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Special Date Price Rule Configuration'));
    }
}
