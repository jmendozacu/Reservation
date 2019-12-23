<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:54
 */
namespace Magenest\Reservation\Model;

use Magenest\Reservation\Model\ResourceModel\ReservationRule as ResourceReservationRule;
use Magenest\Reservation\Model\ResourceModel\ReservationRule\Collection as Collection;

/**
 * Class ReservationRule
 * @package Magenest\Reservation\Model
 */
class ReservationRule extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'reservation_rule';

    /**
     * ReservationRule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceReservationRule $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceReservationRule $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}
