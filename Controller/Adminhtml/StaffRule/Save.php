<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:56
 */
namespace Magenest\Reservation\Controller\Adminhtml\StaffRule;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Magenest\Reservation\Controller\Adminhtml\StaffRule
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Reservation\Model\StaffRuleFactory
     */
    protected $_ruleFactory;

    /**
     * Save constructor.
     * @param \Magenest\Reservation\Model\StaffRuleFactory $ruleFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Magenest\Reservation\Model\StaffRuleFactory $ruleFactory,
        Action\Context $context
    ) {
        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $requestData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($requestData) {
            /** @var \Magenest\Reservation\Model\Rule $model */
            $model = $this->_ruleFactory->create();
            $data = $requestData['rule'];
            if (isset($data['id'])) {
                $model->load($data['id']);
                if ($data['id'] != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save rule.'));
                }
            }

            $model->addData($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Rule has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving rule.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::staff_rule');
    }
}
