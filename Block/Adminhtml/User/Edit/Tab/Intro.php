<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:47
 */
namespace Magenest\Reservation\Block\Adminhtml\User\Edit\Tab;

/***
 * Class Intro
 * @package Magenest\Reservation\Block\Adminhtml\User\Edit\Tab
 */
class Intro extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_LocaleLists;


    /**
     * @var \Magenest\Reservation\Model\StaffFactory
     */
    protected $_introFactory;

    /**
     * @var \Magenest\Reservation\Model\StaffRuleFactory
     */
    protected $_ruleFactory;

    /**
     * Staff constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magenest\Reservation\Model\StaffFactory $staffFactory
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magenest\Reservation\Model\StaffRuleFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magenest\Reservation\Model\StaffFactory $staffFactory,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magenest\Reservation\Model\StaffRuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->_authSession = $authSession;
        $this->_LocaleLists = $localeLists;
        $this->_staffFactory = $staffFactory;
        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $userId = $this->_coreRegistry->registry('permissions_user')->getId();
        $model = $this->_staffFactory->create();
        $form = $this->_formFactory->create();

        $baseFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Staff Information')]
        );

        $form->setHtmlIdPrefix('intro_');

        $currentStaff = $model->getCollection()->addFieldToFilter('staff_id', $userId)->getFirstItem();
        if ($currentStaff->getId()) {
            $baseFieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $ruleData = $this->_ruleFactory->create()->getCollection()->getData();
        $ruleName = [];
        $currentStaffType = $currentStaff->getStaffType();
        $ruleName[$currentStaffType] = $currentStaffType;
        foreach ($ruleData as $item) {
            if ($item['rule_name'] != $currentStaffType) {
                $ruleName[$item['rule_name']] = $item['rule_name'];
            }
        }

        $baseFieldset->addType('img', '\Magenest\Reservation\Block\Adminhtml\User\Img');
        $currentStaffIntro = $currentStaff->getStaffIntro();

        $baseFieldset->addField(
            'staff_intro',
            'textarea',
            [
                'name' => 'staff_intro',
                'label' => __('Self-Description'),
                'required' => false
            ]
        );


        $baseFieldset->addField(
            'staff_type',
            'select',
            [
                'name' => 'staff_type',
                'label' => __('Staff Type'),
                'values' => $ruleName
            ]
        );

        $baseFieldset->addField(
            'avatar_view',
            'img',
            [
                'label' => __('User Image Upload')
            ]
        );

        $data = $model->getData();
        $data['staff_intro'] = $currentStaffIntro;
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __("Staff Information");
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __("Staff Information");
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
