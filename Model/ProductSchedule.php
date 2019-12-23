<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:41
 */
namespace Magenest\Reservation\Model;

/**
 * Class ProductSchedule
 * @package Magenest\Reservation\Model
 */
class ProductSchedule extends \Magento\Framework\Model\AbstractModel
{

    /**
     * ProductSchedule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\ProductSchedule $resource
     * @param ResourceModel\ProductSchedule\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\Reservation\Model\ResourceModel\ProductSchedule $resource,
        \Magenest\Reservation\Model\ResourceModel\ProductSchedule\Collection $resourceCollection,
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
