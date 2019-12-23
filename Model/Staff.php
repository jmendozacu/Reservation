<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:54
 */
namespace Magenest\Reservation\Model;

/**
 * Class Staff
 * @package Magenest\Reservation\Model
 */
class Staff extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'staff';

    /**
     * Staff constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Staff $resource
     * @param ResourceModel\Staff\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\Reservation\Model\ResourceModel\Staff $resource,
        \Magenest\Reservation\Model\ResourceModel\Staff\Collection $resourceCollection,
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
