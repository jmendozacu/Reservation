<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:39
 */
namespace Magenest\Reservation\Model\Plugin;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class AddItem
 * @package Magenest\Reservation\Model\Plugin
 */
class AddItem
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magenest\Reservation\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * AddItem constructor.
     * @param \Magenest\Reservation\Model\ProductFactory $productFactory
     * @param CustomerSession $customerSession
     * @param Cart $cart
     * @param ManagerInterface $managerInterface
     */
    public function __construct(
        \Magenest\Reservation\Model\ProductFactory $productFactory,
        CustomerSession $customerSession,
        Cart $cart,
        ManagerInterface $managerInterface
    ) {
        $this->_productFactory = $productFactory;
        $this->_customerSession = $customerSession;
        $this->_cart = $cart;
        $this->_messageManager = $managerInterface;
    }

    public function aroundAddItem(\Magento\Quote\Model\Quote $subject, \Closure $proceed, \Magento\Quote\Model\Quote\Item $item)
    {
        $product = $item->getProduct();
        $productId = $product->getId();

        $test = $this->_productFactory->create()
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->getFirstItem();

        if ($test->getId()) {
            $list = $this->_cart->getItems();
            if ($list) {
                foreach ($list as $listItem) {
                    if ($listItem->getProduct()->getId() == $productId) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('This product is already added to cart')
                        );
                    }
                }
            }
            $buyRequest = $item->getBuyRequest();
            $options = $buyRequest->getAdditionalOptions();
            $magenestReservationSchedule = $options['reservation_schedule'];
            if ($magenestReservationSchedule == null) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You have to select at least 1 time slot')
                );
            }
        }
        return $proceed($item);
    }
}
