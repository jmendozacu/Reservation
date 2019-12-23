<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:32
 */
namespace Magenest\Reservation\Controller\Adminhtml\Special;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 * @package Magenest\Reservation\Controller\Adminhtml\Special
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Reservation\Model\SpecialFactory
     */
    protected $_specialFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     * @param \Magenest\Reservation\Model\SpecialFactory $specialFactory
     * @param PageFactory $pageFactory
     * @param Registry $registry
     * @param Action\Context $context
     */
    public function __construct(
        \Magenest\Reservation\Model\SpecialFactory $specialFactory,
        PageFactory $pageFactory,
        Registry $registry,
        Action\Context $context
    ) {
        $this->_coreRegistry = $registry;
        $this->_specialFactory = $specialFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Magenest\Reservation\Model\Special $model */
        $model = $this->_specialFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This special date no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('reservation_special', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_Reservation::staff_rule');
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Special Date Rule') : __('New Special Date Rule'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::special_date');
    }
}
