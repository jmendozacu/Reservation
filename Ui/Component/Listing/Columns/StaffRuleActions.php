<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 19/07/2016
 * Time: 15:52
 */
namespace Magenest\Reservation\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class StaffRuleActions
 * @package Magenest\Reservation\Ui\Component\Listing\Columns
 */
class StaffRuleActions extends Column
{
    protected $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'reservation/staffRule/edit',
                        ['id' => $item['id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}
