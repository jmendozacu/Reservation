<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 19/07/2016
 * Time: 15:59
 */
namespace Magenest\Reservation\Ui\DataProvider\Cancel;

use Magenest\Reservation\Model\ResourceModel\Cancel\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class CancelDataProvider extends AbstractDataProvider
{
    protected $collection;

    protected $addFieldStrategies;

    protected $addFilterStrategies;

    public function __construct(
        CollectionFactory $cancelFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta,
        array $data
    ) {
        $this->collection = $cancelFactory->create();
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
