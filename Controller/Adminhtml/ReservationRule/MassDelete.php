<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:57
 */
namespace Magenest\Reservation\Controller\Adminhtml\ReservationRule;

use Magenest\Reservation\Model\ReservationRuleFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Magenest\Reservation\Controller\Adminhtml\ReservationRule
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var ReservationRuleFactory
     */
    protected $_reservationRuleFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param ReservationRuleFactory $reservationRuleFactory
     * @param Registry $registry
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        ReservationRuleFactory $reservationRuleFactory,
        Registry $registry,
        Filter $filter
    ) {
        $this->_filter = $filter;
        $this->_reservationRuleFactory = $reservationRuleFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_reservationRuleFactory->create()->getCollection());
        $deletedReservationRule = 0;
        /** @var \Magenest\Reservation\Model\ReservationRule $item */
        if ($collection) {
            foreach ($collection->getItems() as $item) {
                $item->delete();
                $deletedReservationRule++;
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deletedReservationRule)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('reservation/*/index');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::reservation_rule');
    }
}
