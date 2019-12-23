<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 20/07/2016
 * Time: 00:43
 */
namespace Magenest\Reservation\Ui\DataProvider\StaffRule;

use Magenest\Reservation\Model\ResourceModel\StaffRule\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class StaffRuleDataProvider extends AbstractDataProvider
{
    protected $collection;

    protected $addFieldStrategies;

    protected $addFilterStrategies;

    public function __construct(
        CollectionFactory $staffRuleFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta,
        array $data
    ) {
        $this->collection = $staffRuleFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
