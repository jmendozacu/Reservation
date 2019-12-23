<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 23/07/2016
 * Time: 12:59
 */
namespace Magenest\Reservation\Model;

/**
 * Class User
 * @package Magenest\Reservation\Model
 */
class User extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'User';

    /**
     * User constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\User $resource
     * @param ResourceModel\User\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\Reservation\Model\ResourceModel\User $resource,
        \Magenest\Reservation\Model\ResourceModel\User\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $id
     * @return $this
     */
    public function loadByUserId($id)
    {
        return $this->load($id, 'session_id');
    }
}
