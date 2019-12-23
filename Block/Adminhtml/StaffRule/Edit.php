<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:44
 */
namespace Magenest\Reservation\Block\Adminhtml\StaffRule;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Magenest\Reservation\Block\Adminhtml\StaffRule
 */
class Edit extends FormContainer
{
    protected $_coreRegistry;

    public function __construct(
        Registry $registry,
        Context $context,
        array $data
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Page Constructor
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_Reservation';
        $this->_controller = 'adminhtml_staffRule';
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

        $this->buttonList->update('delete', 'label', __('Delete Template'));
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $templates = $this->_coreRegistry->registry('reservation_staff_rule');
        if ($templates->getId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($templates->getTitle()));
        }
        return __('New Staff Rule');
    }

    /**
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl('reservation/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
}
