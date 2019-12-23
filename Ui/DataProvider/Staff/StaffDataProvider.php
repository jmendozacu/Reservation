<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 19/07/2016
 * Time: 15:59
 */
namespace Magenest\Reservation\Ui\DataProvider\Staff;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Psr\Log\LoggerInterface;

class StaffDataProvider extends DataProvider
{
    protected $_logger;

    protected $_urlInterface;

    protected $_adminSession;

    protected $_userFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        LoggerInterface $loggerInterface,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magenest\Reservation\Model\UserFactory $userFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->_userFactory = $userFactory;
        $this->_logger = $loggerInterface;
        $this->_adminSession = $adminSession;
        $this->_urlInterface = $urlInterface;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $sessionId = $this->_adminSession->getUser()->getId();
        $userId = 0;
        $total = 0;
        $currentUrl = $this->_urlInterface->getCurrentUrl();
        if ($userId == 0 && strpos($currentUrl, 'user_id') !== false) {
            $indexFrom = strpos($currentUrl, 'user_id') + 8;
            $indexTo = strpos($currentUrl, 'key') - 1;
            $userId = substr($currentUrl, $indexFrom, $indexTo - $indexFrom);
            $oldUserSession = $this->_userFactory->create()->getCollection()->addFieldToFilter('session_id', $sessionId)
                ->getFirstItem();
            if ($oldUserSession->getId()) {
                $oldUserSession->setStaffId($userId)->save();
            } else {
                $this->_userFactory->create()->setData([
                    'session_id' => $sessionId,
                    'staff_id' => $userId
                ])->save();
            }
        }
        if ($userId == 0) {
            $oldUserSession = $this->_userFactory->create()->getCollection()->addFieldToFilter('session_id', $sessionId)
                ->getFirstItem();
            if ($oldUserSession->getId()) {
                $userId = $oldUserSession->getStaffId();
            }
        }
        $arrItems = [];
        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            if ($userId != 0) {
                if ($itemData['user_id'] == $userId) {
                    $arrItems['items'][] = $itemData;
                    $total++;
                }
            } else {
                $arrItems['items'][] = $itemData;
                $total++;
            }
        }
        $arrItems['totalRecords'] = $total;
        return $arrItems;
    }
}
