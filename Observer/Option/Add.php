<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:21
 */
namespace Magenest\Reservation\Observer\Option;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Add
 * @package Magenest\Reservation\Observer\Option
 */
class Add implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $product->setHasOptions(true);
    }
}
