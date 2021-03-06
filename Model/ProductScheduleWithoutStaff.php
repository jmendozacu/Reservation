<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:41
 */
namespace Magenest\Reservation\Model;

/**
 * Class ProductScheduleWithoutStaff
 * @package Magenest\Reservation\Model
 */
class ProductScheduleWithoutStaff extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\Reservation\Model\ResourceModel\ProductScheduleWithoutStaff $resource,
        \Magenest\Reservation\Model\ResourceModel\ProductScheduleWithoutStaff\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    public function getDataCollection()
    {
        $collection = $this->getCollection()->addFieldToSelect('*');

        return $collection;
    }
}
