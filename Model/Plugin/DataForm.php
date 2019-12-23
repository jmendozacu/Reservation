<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:44
 */
namespace Magenest\Reservation\Model\Plugin;

use Magento\Backend\Block\Widget\Form;

/**
 * Class RenderPlugin
 * @package Magenest\Reservation\Model\Plugin\View\Layout
 */
class DataForm
{
    /**
     * @param Form $subject
     * @param \Closure $proceed
     * @param $form
     * @return Form
     */
    public function aroundSetForm(Form $subject, \Closure $proceed, $form)
    {
        $proceed($form);
        $data = $subject->getData();
        if (isset($data['module_name']) && isset($data['dest_element_id']) && $data['module_name'] == 'Magento_User') {
            $subject->getForm()->addCustomAttribute('enctype', 'multipart/form-data');
        }
        return $subject;
    }
}
