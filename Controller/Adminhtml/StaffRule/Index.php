<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:33
 */
namespace Magenest\Reservation\Controller\Adminhtml\StaffRule;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Magenest\Reservation\Controller\Adminhtml\StaffRule
 */
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_Reservation::staff_rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Staff Price Rules'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::staff_rule');
    }
}
