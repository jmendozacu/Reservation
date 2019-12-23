<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 18/07/2016
 * Time: 20:36
 */
namespace Magenest\Reservation\Model\Config;

/**
 * Class Options
 * @package Magenest\Reservation\Model\Config
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'confirmed', 'label' => __('Confirmed')],
            ['value' => 'unconfirmed', 'label' => __('Unconfirmed')],
            ['value' => 'canceled', 'label' => __('Canceled')]];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Confirmed'), 1 => __('Unconfirmed'), 2 => __('Canceled')];
    }
}
