<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:11
 */
namespace Magenest\Reservation\Model;

use Magenest\Reservation\Model\ResourceModel\Special as ResourceSpecial;
use Magenest\Reservation\Model\ResourceModel\Special\Collection as Collection;

/**
 * Class Special
 * @package Magenest\Reservation\Model
 */
class Special extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'special';

    /**
     * Special constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceSpecial $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceSpecial $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}
