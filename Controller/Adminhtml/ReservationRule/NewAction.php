<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:57
 */
namespace Magenest\Reservation\Controller\Adminhtml\ReservationRule;

use Magento\Backend\App\Action;

/**
 * Class NewAction
 * @package Magenest\Reservation\Controller\Adminhtml\ReservationRule
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * NewAction constructor.
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Action\Context $context
    ) {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct( $context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->_resultForwardFactory->create();
        return $resultForward->forward('edit');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::reservation_rule');
    }
}
