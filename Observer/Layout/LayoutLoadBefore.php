<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:31
 */
namespace Magenest\Reservation\Observer\Layout;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LayoutLoadBefore
 * @package Magenest\Reservation\Observer\Layout
 */
class LayoutLoadBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $_context;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * LayoutLoadBeforeListener constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_context = $context;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param $observer \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $fullActionName = $observer->getEvent()->getFullActionName();
        $layout = $observer->getEvent()->getLayout();
        $handler = '';

        if ($fullActionName == 'adminhtml_user_edit') {
            $handler = 'reservation_user_handle';
        }
        if ($handler) {
            $layout->getUpdate()->addHandle($handler);
        }
    }
}
