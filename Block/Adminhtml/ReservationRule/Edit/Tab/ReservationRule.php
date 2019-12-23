<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:01
 */
namespace Magenest\Reservation\Block\Adminhtml\ReservationRule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class ReservationRule
 * @package Magenest\Reservation\Block\Adminhtml\ReservationRule\Edit\Tab
 */
class ReservationRule extends Generic implements TabInterface
{

    /**
     * ReservationRule constructor.
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
        $model = $this->_coreRegistry->registry('reservation_reservation_rule');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('reservation_rule_');

        $data = $model->getData();
        $finalData = [];
        if (array_key_exists('id', $data)) {
            $finalData['id'] = $data['id'];
        }
        if (array_key_exists('rule_option', $data) && array_key_exists('rule_name', $data)
            && array_key_exists('rule_function', $data) && array_key_exists('rule_unit', $data)
            && array_key_exists('rule_amount', $data)
        ) {
            $finalData['rule_option'] = $data['rule_option'];
            $finalData['rule_name'] = $data['rule_name'];
            $finalData['rule_function'] = $data['rule_function'];
            $finalData['rule_unit'] = $data['rule_unit'];
            $finalData['rule_amount'] = $data['rule_amount'];
            switch ($data['rule_option']) {
                case 1:
                    $finalData['rule_from_1'] = $data['rule_from'];
                    $finalData['rule_to_1'] = $data['rule_to'];
                    break;
                case 2:
                    $from = $data['rule_from'];
                    $fromArray = explode(",", $from);
                    if ($fromArray[0] == 'Mon') {
                        $finalData['rule_from_2_day'] = 1;
                    } elseif ($fromArray[0] == 'Tue') {
                        $finalData['rule_from_2_day'] = 2;
                    } elseif ($fromArray[0] == 'Wed') {
                        $finalData['rule_from_2_day'] = 3;
                    } elseif ($fromArray[0] == 'Thu') {
                        $finalData['rule_from_2_day'] = 4;
                    } elseif ($fromArray[0] == 'Fri') {
                        $finalData['rule_from_2_day'] = 5;
                    } elseif ($fromArray[0] == 'Sat') {
                        $finalData['rule_from_2_day'] = 6;
                    } elseif ($fromArray[0] == 'Sun') {
                        $finalData['rule_from_2_day'] = 7;
                    }
                    $finalData['rule_from_2_time'] = $fromArray[1];
                    $to = $data['rule_to'];
                    $toArray = explode(",", $to);
                    if ($toArray[0] == 'Mon') {
                        $finalData['rule_to_2_day'] = 1;
                    } elseif ($toArray[0] == 'Tue') {
                        $finalData['rule_to_2_day'] = 2;
                    } elseif ($toArray[0] == 'Wed') {
                        $finalData['rule_to_2_day'] = 3;
                    } elseif ($toArray[0] == 'Thu') {
                        $finalData['rule_to_2_day'] = 4;
                    } elseif ($toArray[0] == 'Fri') {
                        $finalData['rule_to_2_day'] = 5;
                    } elseif ($toArray[0] == 'Sat') {
                        $finalData['rule_to_2_day'] = 6;
                    } elseif ($toArray[0] == 'Sun') {
                        $finalData['rule_to_2_day'] = 7;
                    }
                    $finalData['rule_to_2_time'] = $toArray[1];
                    break;
                case 3:
                    $from = $data['rule_from'];
                    $fromArray = explode(",", $from);
                    $finalData['rule_from_3_day'] = $fromArray[0];
                    $finalData['rule_from_3_time'] = $fromArray[1];
                    $to = $data['rule_to'];
                    $toArray = explode(",", $to);
                    $finalData['rule_to_3_day'] = $toArray[0];
                    $finalData['rule_to_3_time'] = $toArray[1];
                    break;
                case 4:
                    $finalData['rule_from_4'] = $data['rule_from'];
                    $finalData['rule_to_4'] = $data['rule_to'];
                    break;
                case 5:
                    $finalData['rule_5'] = $data['rule_from'];
                    break;
                default:
                    break;
            }
        }


        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('Reservation Price Rule'),
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
            'rule_option',
            'select',
            [
                'name' => 'rule[rule_option]',
                'label' => __('Rule Option'),
                'title' => __('Rule Option'),
                'values' => [
                    '1' => 'Every day',
                    '2' => 'Every week',
                    '3' => 'Every month',
                    '4' => 'Every year',
                    '5' => 'For those orders placed early'
                ]
            ]
        );

        $fieldset->addField(
            'rule_from_1',
            'time',
            [
                'name' => 'rule[rule_from_1]',
                'label' => __('Rule Time From'),
                'title' => __('Rule Time From'),
                'format' => 'hh:mm:ss',
                'time' => true
            ]
        );

        $fieldset->addField(
            'rule_to_1',
            'time',
            [
                'name' => 'rule[rule_to_1]',
                'label' => __('Rule Time To'),
                'title' => __('Rule Time To'),
                'format' => 'hh:mm:ss',
                'time' => true
            ]
        );

        $fieldset->addField(
            'rule_from_2_day',
            'select',
            [
                'name' => 'rule[rule_from_2_day]',
                'label' => __('Rule Day From'),
                'title' => __('Rule Day From'),
                'values' => [
                    '1' => 'Monday',
                    '2' => 'Tuesday',
                    '3' => 'Wednesday',
                    '4' => 'Thursday',
                    '5' => 'Friday',
                    '6' => 'Saturday',
                    '7' => 'Sunday'
                ]
            ]
        );

        $fieldset->addField(
            'rule_from_2_time',
            'time',
            [
                'name' => 'rule[rule_from_2_time]',
                'label' => __('Rule Time From'),
                'title' => __('Rule Time From'),
                'format' => 'hh:mm:ss',
                'style' => 'width: 40%'
            ]
        );

        $fieldset->addField(
            'rule_to_2_day',
            'select',
            [
                'name' => 'rule[rule_to_2_day]',
                'label' => __('Rule Day To'),
                'title' => __('Rule Day To'),
                'values' => [
                    '1' => 'Monday',
                    '2' => 'Tuesday',
                    '3' => 'Wednesday',
                    '4' => 'Thursday',
                    '5' => 'Friday',
                    '6' => 'Saturday',
                    '7' => 'Sunday'
                ]
            ]
        );

        $fieldset->addField(
            'rule_to_2_time',
            'time',
            [
                'name' => 'rule[rule_to_2_time]',
                'label' => __('Rule Time To'),
                'title' => __('Rule Time To'),
                'format' => 'hh:mm:ss',
                'style' => 'width: 40%'
            ]
        );

        $fieldset->addField(
            'rule_from_3_day',
            'select',
            [
                'name' => 'rule[rule_from_3_day]',
                'label' => __('Rule Day From'),
                'title' => __('Rule Day From'),
                'values' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                    '13' => '13',
                    '14' => '14',
                    '15' => '15',
                    '16' => '16',
                    '17' => '17',
                    '18' => '18',
                    '19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22',
                    '23' => '23',
                    '24' => '24',
                    '25' => '25',
                    '26' => '26',
                    '27' => '27',
                    '28' => '28',
                    '29' => '29',
                    '30' => '30',
                    '31' => '31'
                ]
            ]
        );

        $fieldset->addField(
            'rule_from_3_time',
            'time',
            [
                'name' => 'rule[rule_from_3_time]',
                'label' => __('Rule Time From'),
                'title' => __('Rule Time From'),
                'format' => 'hh:mm:ss'
            ]
        );

        $fieldset->addField(
            'rule_to_3_day',
            'select',
            [
                'name' => 'rule[rule_to_3_day]',
                'label' => __('Rule Day To'),
                'title' => __('Rule Day To'),
                'values' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                    '13' => '13',
                    '14' => '14',
                    '15' => '15',
                    '16' => '16',
                    '17' => '17',
                    '18' => '18',
                    '19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22',
                    '23' => '23',
                    '24' => '24',
                    '25' => '25',
                    '26' => '26',
                    '27' => '27',
                    '28' => '28',
                    '29' => '29',
                    '30' => '30',
                    '31' => '31'
                ]
            ]
        );

        $fieldset->addField(
            'rule_to_3_time',
            'time',
            [
                'name' => 'rule[rule_to_3_time]',
                'label' => __('Rule Time To'),
                'title' => __('Rule Time To'),
                'format' => 'hh:mm:ss'
            ]
        );

        $fieldset->addField(
            'rule_from_4',
            'date',
            [
                'name' => 'rule[rule_from_4]',
                'label' => __('Rule Date From'),
                'title' => __('Rule Date From'),
                'time' => true,
                'date_format' => 'MM/dd',
                'time_format' => 'hh:mm:ss a',
                'style' => 'width: 40%'
            ]
        );

        $fieldset->addField(
            'rule_to_4',
            'date',
            [
                'name' => 'rule[rule_to_4]',
                'label' => __('Rule Date To'),
                'title' => __('Rule Date To'),
                'time' => true,
                'date_format' => 'MM/dd',
                'time_format' => 'hh:mm:ss a',
                'style' => 'width: 40%'
            ]
        );

        $fieldset->addField(
            'rule_5',
            'text',
            [
                'name' => 'rule[rule_from_5]',
                'label' => __('Number Of Days'),
                'title' => __('Number Of Days'),
                'style' => 'width: 40%'
            ]
        );

        $fieldset->addField(
            'rule_function',
            'select',
            [
                'name' => 'rule[rule_function]',
                'label' => __('Rule Function'),
                'title' => __('Rule Function'),
                'values' => [
                    '1' => 'Add a fixed amount',
                    '2' => 'Subtract a fixed amount',
                    '3' => 'Add a percentage of origin',
                    '4' => 'Subtract a percentage of origin'
                ],
                'required' => true
            ]
        );

        $fieldset->addField(
            'rule_unit',
            'text',
            [
                'name' => 'rule[rule_unit]',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => true,
                'class' => 'validate-greater-than-zero validate-number-range number-range-0.00-100.00'
            ]
        );

        $fieldset->addField(
            'rule_amount',
            'text',
            [
                'name' => 'rule[rule_amount]',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'class' => 'validate-greater-than-zero input-text',
                'required' => true,
                'style' => 'width: 40%'
            ]
        );

        $form->setValues($finalData);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Reservation Rule');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Reservation Rule');
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
