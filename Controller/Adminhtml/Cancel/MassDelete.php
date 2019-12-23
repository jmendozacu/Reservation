<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 25/07/2016
 * Time: 15:09
 */
namespace Magenest\Reservation\Controller\Adminhtml\Cancel;

use Magenest\Reservation\Model\CancelFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Magenest\Reservation\Controller\Adminhtml\Cancel
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var CancelFactory
     */
    protected $_cancelFactory;

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var \Magenest\Reservation\Model\Email\Mail
     */
    protected $_mail;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_reservationOrderFactory;

    /**
     * MassDelete constructor.
     * @param \Magenest\Reservation\Model\OrderFactory $reservationOrderFactory
     * @param \Magenest\Reservation\Model\Email\Mail $mail
     * @param Action\Context $context
     * @param CancelFactory $cancelFactory
     * @param Filter $filter
     */
    public function __construct(
        \Magenest\Reservation\Model\OrderFactory $reservationOrderFactory,
        \Magenest\Reservation\Model\Email\Mail $mail,
        Action\Context $context,
        CancelFactory $cancelFactory,
        Filter $filter
    ) {
        $this->_reservationOrderFactory = $reservationOrderFactory;
        $this->_mail = $mail;
        $this->_filter = $filter;
        $this->_cancelFactory = $cancelFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_cancelFactory->create()->getCollection());
        $deletedCancel = 0;
        /** @var \Magenest\Reservation\Model\Cancel $item */
        if ($collection) {
            foreach ($collection->getItems() as $item) {
                $item->delete();
                if (intval($item->getUserId()) > 0) {
                    $thisOrder = $this->_reservationOrderFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('order_id', $item->getOrderId())
                        ->addFieldToFilter('order_item_id', $item->getOrderItemId())
                        ->addFieldToFilter('date', $item->getDate())
                        ->addFieldToFilter('from_time', $item->getFromTime())
                        ->addFieldToFilter('to_time', $item->getToTime())
                        ->getFirstItem();
                    $this->_mail->sendRefusedCancelRequestMailToStaff($thisOrder->getId());
                }
                $deletedCancel++;
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deletedCancel)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('reservation/*/index');
    }
}
