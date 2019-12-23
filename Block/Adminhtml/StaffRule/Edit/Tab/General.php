<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:47
 */
namespace Magenest\Reservation\Block\Adminhtml\StaffRule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class General
 * @package Magenest\Reservation\Block\Adminhtml\StaffRule\Edit\Tab
 */
class General extends Generic implements TabInterface
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('reservation_staff_rule');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('staff_rule_');

        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('Staff Price Rule'),
                'class' => 'fieldset-wide'
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'rule[id]']
            );
        }

        $fieldset->addField(
            'rule_name',
            'text',
            [
                'name' => 'rule[rule_name]',
                'label' => __('Rule Name'),
                'title' => __('Rule Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'rule_amount',
            'text',
            [
                'name' => 'rule[rule_amount]',
                'label' => __('Added Amount'),
                'title' => __('Added Amount'),
                'class' => 'validate-number input-text',
                'required' => true
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Staff Price Rule');
    }

    public function getTabTitle()
    {
        return __('Staff Price Rule');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
