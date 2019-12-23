<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:59
 */
namespace Magenest\Reservation\Block\Adminhtml\ReservationRule;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Magenest\Reservation\Block\Adminhtml\ReservationRule
 */
class Edit extends FormContainer
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        array $data
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_Reservation';
        $this->_controller = 'adminhtml_reservationRule';
        parent::_construct();
        $this->buttonList->remove('delete');
        $this->buttonList->update('save', 'label', __('Save Rule'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $templates = $this->_coreRegistry->registry('reservation_reservation_rule');
        if ($templates->getId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($templates->getTitle()));
        }
        return __('New Rule');
    }

    /**
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'reservation/*/save',
            [
                '_current' => true,
                'back' => 'edit',
                'active_tab' => '{{tab_id}}'
            ]
        );
    }
}
