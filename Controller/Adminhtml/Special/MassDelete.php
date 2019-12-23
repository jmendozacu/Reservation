<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 10:31
 */
namespace Magenest\Reservation\Controller\Adminhtml\Special;

use Magenest\Reservation\Model\SpecialFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Magenest\Reservation\Controller\Adminhtml\Special
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var SpecialFactory
     */
    protected $_specialFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param SpecialFactory $specialFactory
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        SpecialFactory $specialFactory,
        Filter $filter
    ) {
        $this->_filter = $filter;
        $this->_specialFactory = $specialFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_specialFactory->create()->getCollection());
        $deletedSpecial = 0;
        /** @var \Magenest\Reservation\Model\Special $item */
        if ($collection) {
            foreach ($collection->getItems() as $item) {
                $item->delete();
                $deletedSpecial++;
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deletedSpecial)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('reservation/*/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Reservation::special_date');
    }
}
