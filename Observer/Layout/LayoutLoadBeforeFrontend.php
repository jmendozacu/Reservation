<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 13/07/2016
 * Time: 10:02
 */
namespace Magenest\Reservation\Observer\Layout;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LayoutLoadBeforeFrontend
 * @package Magenest\Reservation\Observer\Layout
 */
class LayoutLoadBeforeFrontend implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $fullActionName = $observer->getEvent()->getFullActionName();
        /** @var  $layout \Magento\Framework\View\Layout */
        $layout = $observer->getEvent()->getLayout();
        $handler = '';
        if ($fullActionName == 'catalog_product_view') {
            $handler = 'catalog_product_view_reservation';
        }

        if ($handler) {
            $layout->getUpdate()->addHandle($handler);
        }
    }
}
