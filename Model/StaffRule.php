<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:26
 */
namespace Magenest\Reservation\Model;

use Magenest\Reservation\Model\ResourceModel\StaffRule as ResourceStaffRule;
use Magenest\Reservation\Model\ResourceModel\StaffRule\Collection as Collection;

/**
 * Class StaffRule
 * @package Magenest\Reservation\Model
 */
class StaffRule extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'staff_rule';

    /**
     * StaffRule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceStaffRule $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceStaffRule $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}
