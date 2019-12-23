<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:52
 */
namespace Magenest\Reservation\Controller\Adminhtml\ReservationRule;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 * @package Magenest\Reservation\Controller\Adminhtml\ReservationRule
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Reservation\Model\ReservationRuleFactory
     */
    protected $_recurringFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     * @param \Magenest\Reservation\Model\ReservationRuleFactory $recurringFactory
     * @param PageFactory $pageFactory
     * @param Registry $registry
     * @param Action\Context $context
     */
    public function __construct(
        \Magenest\Reservation\Model\ReservationRuleFactory $recurringFactory,
        PageFactory $pageFactory,
        Registry $registry,
        Action\Context $context
    ) {
        $this->_coreRegistry = $registry;
        $this->_recurringFactory = $recurringFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Magenest\Reservation\Model\ReservationRule $model */
        $model = $this->_recurringFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This reservation price rule no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('reservation_reservation_rule', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_Reservation::staff_rule');
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Reservation Price Rule') : __('New Reservation Price Rule'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::reservation_rule');
    }
}
