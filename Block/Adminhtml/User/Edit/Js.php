<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 11:46
 */
namespace Magenest\Reservation\Block\Adminhtml\User\Edit;

/**
 * Class Js
 * @package Magenest\Reservation\Block\Adminhtml\User\Edit
 */
class Js extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magenest\Reservation\Model\StaffFactory
     */
    protected $_introFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Js constructor.
     * @param \Magenest\Reservation\Model\StaffFactory $staffFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magenest\Reservation\Model\StaffFactory $staffFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->_coreRegistry = $registry;
        $this->_staffFactory = $staffFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return int|string
     */
    public function getAvatarImage()
    {
        $userId = $this->_coreRegistry->registry('permissions_user')->getId();
        if ($userId) {
            $data = $this->_staffFactory->create()->getCollection()->addFieldToFilter('staff_id', $userId)->getFirstItem()->getData();
            if ($data) {
                $mediaBaseUrl = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                if (array_key_exists('staff_avatar', $data)) {
                    if ($data['staff_avatar'] != null) {
                        $image = unserialize($data['staff_avatar']);
                        $path = $mediaBaseUrl . 'reservation/user/avatar' . $image['file'];
                        return $path;
                    }
                }
            }
        }
        return 0;
    }
}
