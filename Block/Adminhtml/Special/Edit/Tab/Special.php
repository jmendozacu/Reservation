<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:26
 */
namespace Magenest\Reservation\Block\Adminhtml\Special\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Product
 * @package Magenest\Reservation\Block\Adminhtml\Special\Edit\Tab
 */
class Special extends Generic implements TabInterface
{

    /**
     * Product constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('reservation_special');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('special_');
        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('Special Date Price Rule'),
                'class' => 'fieldset-wide'
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'date[id]']
            );
        }

        $fieldset->addField(
            'date_name',
            'text',
            [
                'name' => 'date[date_name]',
                'label' => __('Rule Name'),
                'title' => __('Rule Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'date_from',
            'date',
            [
                'name' => 'date[date_from]',
                'label' => __('Special Date From'),
                'title' => __('Special Date From'),
                'time' => true,
                'date_format' => 'Y/MM/dd',
                'time_format' => 'hh:mm:ss a',
                'style' => 'width: 40%',
                'required' => true
            ]
        );

        $fieldset->addField(
            'date_to',
            'date',
            [
                'name' => 'date[date_to]',
                'label' => __('Special Date To'),
                'title' => __('Special Date To'),
                'time' => true,
                'date_format' => 'Y/MM/dd',
                'time_format' => 'hh:mm:ss a',
                'style' => 'width: 40%',
                'required' => true
            ]
        );

        $fieldset->addField(
            'date_option',
            'select',
            [
                'name' => 'date[date_option]',
                'label' => __('To'),
                'title' => __('To'),
                'values' => [
                    '1' => 'Add a fixed amount',
                    '2' => 'Subtract a fixed amount'
                ],
                'required' => true
            ]
        );

        $fieldset->addField(
            'date_amount',
            'text',
            [
                'name' => 'date[date_amount]',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'class' => 'validate-number input-text',
                'required' => true,
                'style' => 'width: 40%'
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Special Date Rule');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Special Date Rule');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
