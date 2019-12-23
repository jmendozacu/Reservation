<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 03:54
 */
namespace Magenest\Reservation\Model\Email;

/**
 * Class Mail
 * @package Magenest\Reservation\Model\Email
 */
class Mail
{
    /**
     * @var \Magenest\Reservation\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * Mail constructor.
     * @param \Magenest\Reservation\Model\OrderFactory $orderFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magenest\Reservation\Model\OrderFactory $orderFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_userFactory = $userFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param $reservation_status
     * @param $order_id
     */
    public function sendMailToStaff($reservation_status, $order_id)
    {
        $emailStaffEnable = $this->_scopeConfig->getValue(
            'magenest_order_config/template/email_staff_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($emailStaffEnable != 0) {
            $template_id = 0;
            if ($reservation_status == 'unconfirmed') {
                $template_id = $this->_scopeConfig->getValue(
                    'magenest_order_config/template/staff_unconfirmed',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            } elseif ($reservation_status == 'confirmed') {
                $template_id = $this->_scopeConfig->getValue(
                    'magenest_order_config/template/staff_confirmed',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            } elseif ($reservation_status == 'canceled') {
                $template_id = $this->_scopeConfig->getValue(
                    'magenest_order_config/template/staff_canceled',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            }

            /** @var \Magenest\Reservation\Model\Order $order */
            $order = $this->_orderFactory->create()->load($order_id);

            $thisStaff = $this->_userFactory->create()->load($order->getUserId());
            if (sizeof($thisStaff->getEmail()) > 0) {
                $this->inlineTranslation->suspend();

                $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )->setTemplateVars(
                    [
                        'customerEmail' => $order->getCustomerEmail(),
                        'customerName' => $order->getCustomerName(),
                        'store' => $this->_storeManager->getStore(),
                        'itemName' => $order->getOrderItemName(),
                        'reservationStatus' => $reservation_status,
                        'event' => $order->getSpecialDate(),
                        'date' => $order->getDate(),
                        'start' => $order->getFromTime(),
                        'end' => $order->getToTime()
                    ]
                )->setFrom(
                    $this->_scopeConfig->getValue(
                        'magenest_order_config/config/sender',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )->addTo(
                    $thisStaff->getEmail()
                )->getTransport();

                $transport->sendMessage();
                $this->inlineTranslation->resume();
            }
        }
    }

    public function sendMailToCustomer($reservation_status, $order_id)
    {
        $emailCustomerEnable = $this->_scopeConfig->getValue(
            'magenest_order_config/template/email_customer_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($emailCustomerEnable != 0) {
            $template_id = 0;
            if ($reservation_status == 'unconfirmed') {
                $template_id = $this->_scopeConfig->getValue(
                    'magenest_order_config/template/customer_unconfirmed',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            } elseif ($reservation_status == 'confirmed') {
                $template_id = $this->_scopeConfig->getValue(
                    'magenest_order_config/template/customer_confirmed',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            } elseif ($reservation_status == 'canceled') {
                $template_id = $this->_scopeConfig->getValue(
                    'magenest_order_config/template/customer_canceled',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            }
            /** @var \Magenest\Reservation\Model\Order $order */
            $order = $this->_orderFactory->create()->load($order_id);
            $this->inlineTranslation->suspend();
            $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )->setTemplateVars(
                [
                    'customerEmail' => $order->getCustomerEmail(),
                    'customerName' => $order->getCustomerName(),
                    'store' => $this->_storeManager->getStore(),
                    'itemName' => $order->getOrderItemName(),
                    'reservationStatus' => $reservation_status,
                    'event' => $order->getSpecialDate(),
                    'date' => $order->getDate(),
                    'start' => $order->getFromTime(),
                    'end' => $order->getToTime(),
                    'staffName' => $order->getUserName()
                ]
            )->setFrom(
                $this->_scopeConfig->getValue(
                    'magenest_order_config/config/sender',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->addTo(
                $order->getCustomerEmail()
            )->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }
    }

    public function sendRefusedCancelRequestMailToStaff($order_id)
    {
        $template_id = $this->_scopeConfig->getValue(
            'magenest_order_config/cancel/staff_refuse_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($template_id && intval($template_id) > 0) {
            /** @var \Magenest\Reservation\Model\Order $order */
            $order = $this->_orderFactory->create()->load($order_id);
            if ($order->getId()) {
                $thisStaff = $this->_userFactory->create()->load($order->getUserId());
                if (sizeof($thisStaff->getEmail()) > 0) {
                    $this->inlineTranslation->suspend();

                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customerEmail' => $order->getCustomerEmail(),
                            'customerName' => $order->getCustomerName(),
                            'store' => $this->_storeManager->getStore(),
                            'itemName' => $order->getOrderItemName(),
                            'event' => $order->getSpecialDate(),
                            'date' => $order->getDate(),
                            'start' => $order->getFromTime(),
                            'end' => $order->getToTime()
                        ]
                    )->setFrom(
                        $this->_scopeConfig->getValue(
                            'magenest_order_config/config/sender',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $thisStaff->getEmail()
                    )->getTransport();

                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                }
            }
        }
    }
}
