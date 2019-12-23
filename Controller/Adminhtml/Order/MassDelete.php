<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 04:07
 */
namespace Magenest\Reservation\Controller\Adminhtml\Order;

use Magenest\Reservation\Model\OrderFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param OrderFactory $orderFactory
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        OrderFactory $orderFactory,
        Filter $filter
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_orderFactory->create()->getCollection());
        $deletedOrder = 0;
        /** @var \Magenest\Reservation\Model\Order $item */
        if ($collection) {
            foreach ($collection as $item) {
                if (strcmp($item->getDate(), date('Y/m/d')) < 0) {
                    $item->delete();
                    $deletedOrder++;
                } else {
                    if ($item->getStatus() == 'canceled' || $item->getReservationStatus() == 'canceled') {
                        $item->delete();
                        $deletedOrder++;
                    } else {
                        $this->messageManager->addErrorMessage(
                            __('Reservation Order ID = %1 can not be deleted', $item->getOrderId())
                        );
                    }
                }
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 order(s) have been deleted.', $deletedOrder)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('reservation/*/index');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::orders');
    }
}
