<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 09:34
 */
namespace Magenest\Reservation\Controller\Adminhtml\StaffRule;

use Magenest\Reservation\Model\StaffRuleFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Magenest\Reservation\Controller\Adminhtml\StaffRule
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var StaffRuleFactory
     */
    protected $_staffRuleFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param StaffRuleFactory $staffRuleFactory
     * @param Registry $registry
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        StaffRuleFactory $staffRuleFactory,
        Registry $registry,
        Filter $filter
    ) {
        $this->_filter = $filter;
        $this->_staffRuleFactory = $staffRuleFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_staffRuleFactory->create()->getCollection());
        $deletedStaffRule = 0;
        /** @var \Magenest\Reservation\Model\StaffRule $item */
        if ($collection) {
            foreach ($collection as $item) {
                $item->delete();
                $deletedStaffRule++;
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deletedStaffRule)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('reservation/*/index');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::staff_rule');
    }
}
