<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 19/07/2016
 * Time: 14:36
 */
namespace Magenest\Reservation\Controller\Customer;

/**
 * Class Cancel
 * @package Magenest\Reservation\Controller\Customer
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magenest\Reservation\Model\CancelFactory
     */
    protected $_cancelFactory;

    protected $_logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\Reservation\Model\OrderFactory $orderFactory,
        \Magenest\Reservation\Model\CancelFactory $cancelFactory
    ) {
        $this->_logger = $logger;
        $this->_customerSession = $customerSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_orderFactory = $orderFactory;
        $this->_cancelFactory = $cancelFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Cancel Request Sent'));
        $params = $this->getRequest()->getParams();

        /** @var \Magenest\Reservation\Model\Cancel $cancelModel */
        $cancelModel = $this->_cancelFactory->create();

        /** @var \Magenest\Reservation\Model\Order $orderModel */
        $orderModel = $this->_orderFactory->create();

        $collection = $orderModel->getCollection()->addFieldToFilter('order_id', $params['order_id'])
            ->addFieldToFilter('order_item_id', $params['order_item_id'])
            ->addFieldToFilter('from_time', $params['from_time'])
            ->addFieldToFilter('to_time', $params['to_time'])
            ->addFieldToFilter('user_id', $params['user_id'])
            ->addFieldToFilter('slots', $params['slots'])->getFirstItem();
        $date = $params['date'];
        $date = str_replace('-', '/', $date);
        if ($collection->getId()) {
            $oldCancel = $this->_cancelFactory->create()
                ->getCollection()
                ->addFieldToFilter('order_id', $params['order_id'])
                ->addFieldToFilter('order_item_id', $params['order_item_id'])
                ->getFirstItem();
            if (!$oldCancel->getId()) {
                $cancelData = array(
                    'order_id' => $params['order_id'],
                    'order_item_id' => $params['order_item_id'],
                    'order_item_name' => $params['order_item_name'],
                    'customer_id' => $params['customer_id'],
                    'customer_email' => $params['customer_email'],
                    'customer_name' => $params['customer_name'],
                    'status' => $params['status'],
                    'date' => $date,
                    'from_time' => $params['from_time'],
                    'to_time' => $params['to_time'],
                    'slots' => $params['slots'],
                    'user_id' => $params['user_id'],
                    'user_name' => $params['user_name'],
                    'reservation_status' => $params['reservation_status']
                );
                $cancelModel->setData($cancelData)->save();
                $collection->setData('cancel_request', 1)->save();
            }
        }
        return $resultPage;
    }
}
