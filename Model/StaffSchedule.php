<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:52
 */
namespace Magenest\Reservation\Model;

/**
 * Class StaffSchedule
 * @package Magenest\Reservation\Model
 */
class StaffSchedule extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'intro';

    /**
     * StaffSchedule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\StaffSchedule $resource
     * @param ResourceModel\StaffSchedule\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\Reservation\Model\ResourceModel\StaffSchedule $resource,
        \Magenest\Reservation\Model\ResourceModel\StaffSchedule\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $id
     * @return $this
     */
    public function loadByStaffId($id)
    {
        return $this->load($id, 'staff_id');
    }
}
