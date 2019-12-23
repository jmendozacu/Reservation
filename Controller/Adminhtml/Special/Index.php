<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:09
 */
namespace Magenest\Reservation\Controller\Adminhtml\Special;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Magenest\Reservation\Controller\Adminhtml\Special
 */
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_Reservation::staff_rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Special Date Price Rules'));
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
