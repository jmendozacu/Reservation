<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 03:10
 */
namespace Magenest\Reservation\Model;

use Magenest\Reservation\Model\ResourceModel\Order as ResourceOrder;
use Magenest\Reservation\Model\ResourceModel\Order\Collection as Collection;

/**
 * Class Order
 * @package Magenest\Reservation\Model
 */
class Order extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'reservation_order';

    /**
     * Order constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceOrder $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceOrder $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}
