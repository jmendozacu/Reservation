<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:45
 */
namespace Magenest\Reservation\Block\Adminhtml\User;

/**
 * Class Img
 * @package Magenest\Reservation\Block\Adminhtml\User
 */
class Img extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $html = $html . '<input id="reservation_user_upload" name="intro_avatar" type="file" /><br><br><br>';
        $html = $html . '<input id="reservation_is_deleted" name="intro_is_deleted" value="" style="display: none" />';
        $html = $html . '<div id="reservation_avatar_container">';
        $html = $html . '<img id="reservation_user_avatar" src="" /><br>';
        $html = $html . '<button id="reservation_delete_avatar" title="Remove" type="button">Remove</button>';
        $html = $html . '</div>';

        return $html;
    }
}
