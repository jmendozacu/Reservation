<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:57
 */
namespace Magenest\Reservation\Controller\Adminhtml\ReservationRule;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 * @package Magenest\Reservation\Controller\Adminhtml\ReservationRule
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Reservation\Model\ReservationRuleFactory
     */
    protected $_reservationRuleFactory;

    /**
     * Save constructor.
     * @param \Magenest\Reservation\Model\ReservationRuleFactory $reservationRuleFactory
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Magenest\Reservation\Model\ReservationRuleFactory $reservationRuleFactory,
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        $this->_reservationRuleFactory = $reservationRuleFactory;
        parent::__construct( $context);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $requestData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($requestData) {
            $finalData = [];
            /** @var \Magenest\Reservation\Model\ReservationRule $model */
            $model = $this->_reservationRuleFactory->create();
            $data = $requestData['rule'];
            if (isset($data['id'])) {
                $finalData['id'] = $data['id'];
                $model->load($data['id']);
                if ($data['id'] != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save rule.'));
                }
            }
            $finalData['rule_name'] = $data['rule_name'];
            $finalData['rule_option'] = $data['rule_option'];
            $finalData['rule_function'] = $data['rule_function'];
            $finalData['rule_unit'] = $data['rule_unit'];
            $finalData['rule_amount'] = $data['rule_amount'];
            if ($data['rule_option'] == 1) {
                $finalData['rule_from'] = $data['rule_from_1'][0] . ':' . $data['rule_from_1'][1] . ':' . $data['rule_from_1'][2];
                $finalData['rule_to'] = $data['rule_to_1'][0] . ':' . $data['rule_to_1'][1] . ':' . $data['rule_to_1'][2];
            } elseif ($data['rule_option'] == 2) {
                $from = $data['rule_from_2_day'];
                if ($from == '1') {
                    $from = 'Mon';
                } elseif ($from == '2') {
                    $from = 'Tue';
                } elseif ($from == '3') {
                    $from = 'Wed';
                } elseif ($from == '4') {
                    $from = 'Thu';
                } elseif ($from == '5') {
                    $from = 'Fri';
                } elseif ($from == '6') {
                    $from = 'Sat';
                } elseif ($from == '7') {
                    $from = 'Sun';
                }
                $to = $data['rule_to_2_day'];
                if ($to == '1') {
                    $to = 'Mon';
                } elseif ($to == '2') {
                    $to = 'Tue';
                } elseif ($to == '3') {
                    $to = 'Wed';
                } elseif ($to == '4') {
                    $to = 'Thu';
                } elseif ($to == '5') {
                    $to = 'Fri';
                } elseif ($to == '6') {
                    $to = 'Sat';
                } elseif ($to == '7') {
                    $to = 'Sun';
                }
                $finalData['rule_from'] = $from . ',' . $data['rule_from_2_time'][0] . ':' . $data['rule_from_2_time'][1] . ':' . $data['rule_from_2_time'][2];
                $finalData['rule_to'] = $to . ',' . $data['rule_to_2_time'][0] . ':' . $data['rule_to_2_time'][1] . ':' . $data['rule_to_2_time'][2];
            } elseif ($data['rule_option'] == 3) {
                $finalData['rule_from'] = $data['rule_from_3_day'] . ',' . $data['rule_from_3_time'][0] . ':' . $data['rule_from_3_time'][1] . ':' . $data['rule_from_3_time'][2];
                $finalData['rule_to'] = $data['rule_to_3_day'] . ',' . $data['rule_to_3_time'][0] . ':' . $data['rule_to_3_time'][1] . ':' . $data['rule_to_3_time'][2];
            } elseif ($data['rule_option'] == 4) {
                $finalData['rule_from'] = strtoupper($data['rule_from_4']);
                $finalData['rule_to'] = strtoupper($data['rule_to_4']);
            } elseif ($data['rule_option'] == 5) {
                $rule_5 = intval($data['rule_from_5']);
                if ($rule_5 < 0) {
                    $this->messageManager->addErrorMessage("You just fill a negative number");
                    $rule_5 = 0 - $rule_5;
                }
                if ($rule_5 == 0) {
                    $this->messageManager->addErrorMessage("We just set number of days = 0");
                }
                $finalData['rule_from'] = $rule_5;
                $finalData['rule_to'] = '';
                if ($finalData['rule_amount'] >= 100) {
                    $finalData['rule_amount'] = 100;
                    $this->messageManager->addErrorMessage(__('Amount can not be a number greater then 100'));
                }
            }
            $model->addData($finalData);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Rule has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving rule.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::reservation_rule');
    }
}
