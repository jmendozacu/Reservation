<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:13
 */
namespace Magenest\Reservation\Model;

use Magenest\Reservation\Model\ResourceModel\Cancel as ResourceCancel;
use Magenest\Reservation\Model\ResourceModel\Cancel\Collection as Collection;

/**
 * Class Cancel
 * @package Magenest\Reservation\Model
 */
class Cancel extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'cancel';

    /**
     * Cancel constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceCancel $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceCancel $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}
