<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:31
 */
namespace Magenest\Reservation\Controller\Adminhtml\Special;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 * @package Magenest\Reservation\Controller\Adminhtml\Special
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Reservation\Model\SpecialFactory
     */
    protected $_specialFactory;

    /**
     * Save constructor.
     * @param \Magenest\Reservation\Model\SpecialFactory $specialFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Magenest\Reservation\Model\SpecialFactory $specialFactory,
        Action\Context $context
    ) {
        $this->_specialFactory = $specialFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $requestData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($requestData) {
            /** @var \Magenest\Reservation\Model\Special $model */
            $model = $this->_specialFactory->create();
            $data = $requestData['date'];
            if (isset($data['id'])) {
                $model->load($data['id']);
                if ($data['id'] != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save rule.'));
                }
            }

            $data['date_from'] = strtoupper($data['date_from']);
            $data['date_to'] = strtoupper($data['date_to']);
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
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::special_date');
    }
}
