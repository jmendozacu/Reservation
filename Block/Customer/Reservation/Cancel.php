<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 19/07/2016
 * Time: 14:20
 */
namespace Magenest\Reservation\Block\Customer\Reservation;

/**
 * Class Cancel
 * @package Magenest\Reservation\Block\Customer\Reservation
 */
class Cancel extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Cancel constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getCancelRequestData()
    {
        $data = $this->getRequest()->getParams();
        return $data;
    }
}
