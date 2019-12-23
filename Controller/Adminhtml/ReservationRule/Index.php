<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:56
 */
namespace Magenest\Reservation\Controller\Adminhtml\ReservationRule;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Magenest\Reservation\Controller\Adminhtml\ReservationRule
 */
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_Reservation::staff_rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Reservation Price Rules'));
        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::reservation_rule');
    }
}
