<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:01
 */
namespace Magenest\Reservation\Block\Adminhtml\ReservationRule\Edit;

use Magento\Backend\Block\Template\Context;

/**
 * Class Js
 * @package Magenest\Reservation\Block\Adminhtml\ReservationRule\Edit
 */
class Js extends \Magento\Backend\Block\Template
{
    protected $_registry;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getTimeList()
    {
        $model = $this->_registry->registry('reservation_reservation_rule');
        $reservationData = $model->getData();
        if (array_key_exists('rule_option', $reservationData)) {
            switch ($reservationData['rule_option']) {
                case 1:
                    $fromArray = explode(":", $reservationData['rule_from']);
                    $toArray = explode(":", $reservationData['rule_to']);
                    $result = [];
                    array_push($result, $fromArray);
                    array_push($result, $toArray);
                    return $result;
                    break;
                case 2:
                    $from = explode(",", $reservationData['rule_from']);
                    $fromString = $from[1];
                    $fromArray = explode(":", $fromString);
                    $to = explode(",", $reservationData['rule_to']);
                    $toString = $to[1];
                    $toArray = explode(":", $toString);
                    $result = [];
                    array_push($result, $fromArray);
                    array_push($result, $toArray);
                    return $result;
                    break;
                case 3:
                    $from = explode(",", $reservationData['rule_from']);
                    $fromString = $from[1];
                    $fromArray = explode(":", $fromString);
                    $to = explode(",", $reservationData['rule_to']);
                    $toString = $to[1];
                    $toArray = explode(":", $toString);
                    $result = [];
                    array_push($result, $fromArray);
                    array_push($result, $toArray);
                    return $result;
                    break;
                default:
                    break;
            }
        }
        return null;
    }
}
