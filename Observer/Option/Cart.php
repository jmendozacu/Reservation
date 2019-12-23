<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:22
 */
namespace Magenest\Reservation\Observer\Option;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;


/**
 * Class Cart
 * @package Magenest\Reservation\Observer\Option
 */
class Cart implements ObserverInterface
{
    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    protected $_logger;

    /**
     * @var \Magenest\Reservation\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;


    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Reservation\Model\ProductFactory $productFactory,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null

    ) {
        $this->_logger = $logger;
        $this->_productFactory = $productFactory;
        $this->_currency = $currency;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getQuoteItem();
        $product = $item->getProduct();
        $productId = $product->getId();

        $test = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->getFirstItem();
        if ($test->getId()) {
            $buyRequest = $item->getBuyRequest();
            $options = $buyRequest->getAdditionalOptions();
            $magenestReservationSchedule = $options['reservation_schedule'];
            if ($magenestReservationSchedule == null) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You have to choose at least 1 slot')
                );
            } else {
                $product = $observer->getEvent()->getProduct();
                $magenestAdditionalOptionsJson = $options['reservation_schedule'];
                $magenestAdditionalOptionsArray = (array)json_decode($magenestAdditionalOptionsJson);
                switch ($magenestAdditionalOptionsArray[0]) {
                    case 0:
                        $additionalOptions = [];
                        $additionalOptions[] = array(
                            'label' => 'Reservation Option',
                            'value' => 'Some hours in 1 day with staff'
                        );
                        $additionalOptions[] = array(
                            'label' => 'Reservation Date',
                            'value' => $options['reservation_date']
                        );
                        $finalPrice = 0;
                        for ($i = 1; $i < sizeof($magenestAdditionalOptionsArray); $i++) {
                            $additionalOptions[] = [
                                'label' => "From " . $magenestAdditionalOptionsArray[$i][4] . " To " . $magenestAdditionalOptionsArray[$i][5],
                                'value' => $magenestAdditionalOptionsArray[$i][7] . '|' . $magenestAdditionalOptionsArray[$i][0] . "|" . $magenestAdditionalOptionsArray[$i][1] . "| " . $magenestAdditionalOptionsArray[$i][9] . $magenestAdditionalOptionsArray[$i][8]
                            ];
                            $finalPrice += $magenestAdditionalOptionsArray[$i][8];
                        }
                        $item->addOption(array(
                            'code' => 'additional_options',
                            'value' => $this->serializer->serialize($additionalOptions)
                        ));
                        $item->setCustomPrice($finalPrice);
                        $item->setOriginalCustomPrice($finalPrice);
                        $item->setQty(1);
                        break;
                    case 1:
                        $additionalOptions = [];
                        $additionalOptions[] = array(
                            'label' => 'Reservation Option',
                            'value' => 'Full day with staff'
                        );
                        $finalPrice = 0;
                        $scheduleArray = explode('|', $magenestAdditionalOptionsArray[1]);
                        for ($i = 1; $i < sizeof($scheduleArray); $i++) {
                            $scheduleArrayItem = (array)json_decode($scheduleArray[$i]);
                            $additionalOptions[] = [
                                'label' => $scheduleArrayItem['date'],
                                'value' => $scheduleArrayItem['event_name'] . '|' . $scheduleArrayItem['staff_id'] . '|' . $scheduleArrayItem['staff_name'] . '| ' . $scheduleArrayItem['symbol'] . $scheduleArrayItem['event_amount']
                            ];
                            $finalPrice += $scheduleArrayItem['event_amount'];
                        }
                        $item->addOption(array(
                            'code' => 'additional_options',
                            'value' => $this->serializer->serialize($additionalOptions)
                        ));
                        $item->setCustomPrice($finalPrice);
                        $item->setOriginalCustomPrice($finalPrice);
                        $item->setQty(1);
                        break;
                    case 2:
                        $additionalOptions = [];
                        $additionalOptions[] = array(
                            'label' => 'Reservation Option',
                            'value' => 'Some hours in 1 day without staff'
                        );
                        $additionalOptions[] = array(
                            'label' => 'Reservation Date',
                            'value' => $options['reservation_date']
                        );
                        $finalPrice = 0;
                        for ($i = 1; $i < sizeof($magenestAdditionalOptionsArray); $i++) {
                            $additionalOptions[] = [
                                'label' => "From " . $magenestAdditionalOptionsArray[$i][0] . " To " . $magenestAdditionalOptionsArray[$i][1],
                                'value' => $magenestAdditionalOptionsArray[$i][2] . '|' . $magenestAdditionalOptionsArray[$i][3] . " slot(s)| " . $magenestAdditionalOptionsArray[$i][5] . $magenestAdditionalOptionsArray[$i][4]
                            ];
                            $finalPrice += $magenestAdditionalOptionsArray[$i][4];
                        }
                        $item->addOption(array(
                            'code' => 'additional_options',
                            'value' => $this->serializer->serialize($additionalOptions)
                        ));
                        $item->setCustomPrice($finalPrice);
                        $item->setOriginalCustomPrice($finalPrice);
                        $item->setQty(1);
                        break;
                    case 3:
                        $additionalOptions = [];
                        $finalPrice = 0;
                        $additionalOptions[] = array(
                            'label' => 'Reservation Option',
                            'value' => 'Full day without staff'
                        );
                        $cartAllData = explode('|', $magenestAdditionalOptionsArray[1]);
                        $cartAllDataArray = [];
                        for ($i = 1; $i < sizeof($cartAllData); $i++) {
                            $cartAllDataArrayTemp = (array)json_decode($cartAllData[$i]);
                            $cartAllDataArray[] = (array)$cartAllDataArrayTemp[0];
                        }
                        $slotArray = explode(';', $magenestAdditionalOptionsArray[2]);
                        for ($i = 1; $i < sizeof($slotArray); $i++) {
                            $slotArrayItem = explode(',', $slotArray[$i]);
                            foreach ($cartAllDataArray as $cartAllDataArrayItem) {
                                if ($slotArrayItem[0] == $cartAllDataArrayItem['date']) {
                                    $finalPrice += (int)$slotArrayItem[1] * (float)$cartAllDataArrayItem['event_amount'];
                                    $additionalOptions[] = array(
                                        'label' => $slotArrayItem[0],
                                        'value' => $cartAllDataArrayItem['event_name'] . '|' . $slotArrayItem[1] . ' slot(s)'
                                    );
                                }
                            }
                        }
                        $item->addOption(array(
                            'code' => 'additional_options',
                            'value' => $this->serializer->serialize($additionalOptions)
                        ));
                        $item->setCustomPrice($finalPrice);
                        $item->setOriginalCustomPrice($finalPrice);
                        $item->setQty(1);
                        break;
                    default:
                        break;
                }
            }
        }
    }
}
