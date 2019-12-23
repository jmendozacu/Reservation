<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 13/07/2016
 * Time: 10:34
 */
namespace Magenest\Reservation\Controller\Product;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Time10
 * @package Magenest\Reservation\Controller\Product
 */
class Time10 extends \Magento\Framework\App\Action\Action
{
    protected $_logger;

    /**
     * @var \Magenest\Reservation\Model\ProductScheduleFactory
     */
    protected $_productScheduleFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magento\Directory\Model\Currency $currency
    ) {
        $this->_logger = $logger;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_currency = $currency;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = [];
        $params = $this->getRequest()->getParams();
        $productId = $params['product_id'];
        $monthYearArray = explode(' ', $params['date']);
        $thisMonth = $monthYearArray[0];
        $thisYear = $monthYearArray[1];
        if ($thisMonth == 'January') {
            $thisMonth = '01';
        } elseif ($thisMonth == 'February') {
            $thisMonth = '02';
        } elseif ($thisMonth == 'March') {
            $thisMonth = '03';
        } elseif ($thisMonth == 'April') {
            $thisMonth = '04';
        } elseif ($thisMonth == 'May') {
            $thisMonth = '05';
        } elseif ($thisMonth == 'June') {
            $thisMonth = '06';
        } elseif ($thisMonth == 'July') {
            $thisMonth = '07';
        } elseif ($thisMonth == 'August') {
            $thisMonth = '08';
        } elseif ($thisMonth == 'September') {
            $thisMonth = '09';
        } elseif ($thisMonth == 'October') {
            $thisMonth = '10';
        } elseif ($thisMonth == 'November') {
            $thisMonth = '11';
        } elseif ($thisMonth == 'December') {
            $thisMonth = '12';
        }
        $schedules = $this->_productScheduleFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
        $thisMonthDayNum = cal_days_in_month(CAL_GREGORIAN, $thisMonth, $thisYear);
        for ($i = 1; $i <= $thisMonthDayNum; $i++) {
            $j = $i;
            if ($j < 10) {
                $j = '0' . $j;
            }
            $currentDate = $thisMonth . '/' . $j . '/' . $thisYear;
            $currentDay = date('N', strtotime($currentDate));
            if (strcmp(date('Y/m/d', strtotime($currentDate)), date('Y/m/d')) > 0) {
                foreach ($schedules as $schedule) {
                    $oldOrders = unserialize($schedule->getOrders());
                    $orderAdded = 0;
                    if ($oldOrders) {
                        foreach ($oldOrders as $oldOrder) {
                            if ($oldOrder == date('Y/m/d', strtotime($currentDate)) && $schedule->getWeekday() == $currentDay) {
                                $orderAdded = 1;
                                break;
                            }
                        }
                    }
                    if ($orderAdded == 0) {
                        if ($schedule->getWeekday() == $currentDay) {
                            $dateAdded = 0;
                            if (sizeof($result) > 0) {
                                foreach ($result as $resultItem) {
                                    if ($resultItem == $i) {
                                        $dateAdded = 1;
                                        break;
                                    }
                                }
                            }
                            if ($dateAdded == 0) {
                                $result[] = $i;
                            }
                        }
                    }
                }
            }
        }
        $resultArray = json_encode($result);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($resultArray);
        return $resultJson;
    }
}
