<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:48
 */
namespace Magenest\Reservation\Block\Adminhtml\User\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Schedule
 * @package Magenest\Reservation\Block\Adminhtml\User\Edit\Tab
 */
class Schedule extends Generic implements TabInterface
{
    const CURRENT_USER_PASSWORD_FIELD = 'current_password';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magenest\Reservation\Model\StaffFactory
     */
    protected $_staffScheduleFactory;

    /**
     * @var string
     */
    protected $_template = 'user/edit/tab/table.phtml';

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_LocaleLists;

    /**
     * Schedule constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory,
        array $data = []
    ) {
        $this->_authSession = $authSession;
        $this->_LocaleLists = $localeLists;
        $this->_staffScheduleFactory = $staffScheduleFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __("Work Schedule");
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __("Work Schedule");
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getStaffCollection()
    {
        $model = $this->_staffScheduleFactory->create();

        $data = $model->getCollection()->addFieldToFilter('staff_id', $this->getCurrentUserId());
        if ($data == null) {
            return [];
        } else {
            $result = [];
            foreach ($data as $dataItem) {
                if ($dataItem->getFromTime() == null) {
                    $option = 1;
                } else {
                    $option = 0;
                }
                array_push(
                    $result,
                    [
                        'option' => $option,
                        'weekday' => $dataItem->getWeekday(),
                        'product_id' => $dataItem->getProductId(),
                        'from_time' => $dataItem->getFromTime(),
                        'to_time' => $dataItem->getToTime()
                    ]
                );
            }
            usort(
                $result,
                function ($data1, $data2) {
                    if ($data1['product_id'] == $data2['product_id']) {
                        return 0;
                    }
                    return ($data1['product_id'] > $data2['product_id']) ? -1 : 1;
                }
            );
            usort(
                $result,
                function ($data1, $data2) {
                    if ($data1['weekday'] == $data2['weekday']) {
                        return 0;
                    }
                    return ($data1['weekday'] > $data2['weekday']) ? 1 : -1;
                }
            );
            if (sizeof($result) > 1) {
                for ($i = 0; $i < sizeof($result) - 1; $i++) {
                    for ($j = $i + 1; $j < sizeof($result); $j++) {
                        if ($result[$i]['weekday'] == $result[$j]['weekday'] && $result[$i]['product_id'] == $result[$j]['product_id']
                        && $result[$i]['from_time'] > $result[$j]['from_time']
                        ) {
                            $temp = $result[$i];
                            $result[$i] = $result[$j];
                            $result[$j] = $temp;
                        }
                    }
                }
            }
            return $result;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentUserId()
    {
        $user_id = $this->_coreRegistry->registry('permissions_user')->getId();

        return $user_id;
    }
}
