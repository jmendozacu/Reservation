<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 18/07/2016
 * Time: 20:12
 */
namespace Magenest\Reservation\Block\Customer\Reservation;

/**
 * Class ReservationList
 * @package Magenest\Reservation\Block\Customer\Reservation
 */
class ReservationList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_orderFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magenest\Reservation\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCancelOrders()
    {
        $currentCustomerId = $this->currentCustomer->getCustomerId();

        $orderCollection = $this->_orderFactory->create()
            ->getCollection()->addFieldToFilter('customer_id', $currentCustomerId);
        $data = $orderCollection->getData();
        $number = $default_status = $this->_scopeConfig->getValue(
            'magenest_order_config/cancel/number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $finalData = [];
        foreach ($data as $dataItem) {
            $diff = date_diff(date_create($dataItem['date']), date_create(date('Y/m/d')));
            $diffInt = intval($diff->format("%a"));
            $dataItem['cancel'] = false;
            if (intval($diffInt) > intval($number)) {
                $dataItem['cancel'] = true;
            }
            $dataItem['date'] = str_replace('/', '-', $dataItem['date']);
            $finalData[] = $dataItem;
        }
        usort(
            $finalData,
            function ($data1, $data2) {
                if ($data1['date'] == $data2['date']) {
                    return 0;
                }
                return ($data1['date'] > $data2['date']) ? 1 : -1;
            }
        );
        return $finalData;
    }

    /**
     * @param $order_id
     * @return string
     */
    public function getViewUrl($order_id)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order_id]);
    }

    /**
     * @param $item
     * @return string
     */
    public function getReservationCancelUrl($item)
    {
        return $this->getUrl('reservation/customer/cancel', $item);
    }
}
