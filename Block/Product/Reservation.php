<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 07/07/2016
 * Time: 14:03
 */
namespace Magenest\Reservation\Block\Product;

/**
 * Class Reservation
 * @package Magenest\Reservation\Block\Product
 */
class Reservation extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        array $data,
        \Magenest\Reservation\Model\ProductFactory $productFactory
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCurrentProductId()
    {
        $id = $this->_coreRegistry->registry('current_product')->getId();
        return $id;
    }

    /**
     * @return bool
     */
    public function isReservationProduct()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $thisProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $product->getId())->getFirstItem();
        if ($thisProduct->getId()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isStaffNecessary()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $thisProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $product->getId())->getFirstItem();
        if ($thisProduct->getId()) {
            if ($thisProduct->getNeedStaff() == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getOption()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $thisProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $product->getId())->getFirstItem();
        if ($thisProduct->getId()) {
            return $thisProduct->getOption();
        }
        return 0;
    }
}
