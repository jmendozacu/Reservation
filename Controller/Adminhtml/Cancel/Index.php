<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:10
 */
namespace Magenest\Reservation\Controller\Adminhtml\Cancel;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Magenest\Reservation\Controller\Adminhtml\Cancel
 */
class Index extends Action
{
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_Reservation::cancel');
        $resultPage->getConfig()->getTitle()->prepend(__('Cancel Requests'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::cancel');
    }
}
