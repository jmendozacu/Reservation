<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:29
 */
namespace Magenest\Reservation\Observer\Staff;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\ObserverInterface;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * Class Save
 * @package Magenest\Reservation\Observer\Staff
 */
class Save implements ObserverInterface
{
    protected $_logger;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magenest\Reservation\Model\StaffFactory
     */
    protected $_staffFactory;

    /**
     * @var \Magenest\Reservation\Model\StaffScheduleFactory
     */
    protected $_staffScheduleFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productScheduleFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magenest\Reservation\Model\StaffFactory $staffFactory,
        \Magenest\Reservation\Model\StaffScheduleFactory $staffScheduleFactory,
        \Magenest\Reservation\Model\ProductScheduleFactory $productScheduleFactory,
        \Magenest\Reservation\Model\ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Registry $registry,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->_logger = $logger;
        $this->_productScheduleFactory = $productScheduleFactory;
        $this->_staffFactory = $staffFactory;
        $this->_staffScheduleFactory = $staffScheduleFactory;
        $this->_productFactory = $productFactory;
        $this->_adapterFactory = $adapterFactory;
        $this->_request = $request;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_messageManager = $messageManager;
        $this->_coreRegistry = $registry;
        $this->_userFactory = $userFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->_request->getParams();
        $userId = null;
        //Hau
        if (isset($params['user_id'])) {
            $userId = $params['user_id'];
        }

        if ($userId == null) {
            $userModel = $this->_userFactory->create();
            if (array_key_exists('email', $params)) {
                $userId = $userModel->getCollection()->addFieldToFilter('email', $params['email'])->getFirstItem()->getUserId();
            }
        }

        if ($userId != null) {
            $staffFactory = $this->_staffFactory->create();
            $staffInfo = $staffFactory->getCollection()->addFieldToFilter('staff_id', $userId)->getFirstItem();
            $data = [
                'staff_name' => $params['firstname'] . ' ' . $params['lastname'],
                'staff_intro' => $params['staff_intro'],
                'staff_id' => $userId,
                'staff_avatar' => $staffInfo->getStaffAvatar()
            ];
            if (array_key_exists('staff_type', $params)) {
                $data['staff_type'] = $params['staff_type'];
            }
            if ($params['intro_is_deleted']) {
                $oldImageString = $staffInfo->getStaffAvatar();
                $oldImage = unserialize($oldImageString);
                $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

                if ($path->isFile('reservation/user/avatar' . $oldImage['file'])) {
                    $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                        ->delete('reservation/user/avatar' . $oldImage['file']);
                }
                $data['staff_avatar'] = null;

                try {
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'intro_avatar']);
                    $base_media_path = 'reservation/user/avatar';
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
                    $imageAdapter = $this->_adapterFactory->create();
                    $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($base_media_path));
                    if (is_array($result) && !empty($result['name'])) {
                        $thisStaffAvatar = $staffFactory->getCollection()->addFieldToFilter('staff_id', $userId)->getFirstItem();
                        $data['staff_avatar'] = serialize($result);
                        $oldImage = unserialize($thisStaffAvatar->getAvatar());
                        $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                        if ($path->isFile('reservation/user/avatar' . $oldImage['file'])) {
                            $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                ->delete('reservation/user/avatar' . $oldImage['file']);
                        }
                    }
                } catch (\Exception $e) {
                    if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                    }
                }
            }
            if ($staffInfo->getStaffId()) {
                $staffInfo->addData($data)->save();
            } else {
                $staffInfo->setData($data)->save();
            }
        }

        if (array_key_exists('staff', $params) && $userId != null) {
            /** @var \Magento\User\Model\User $userModel */
            $productModel = $this->_productFactory->create();
            $productScheduleModel = $this->_productScheduleFactory->create();
            $staffScheduleModel = $this->_staffScheduleFactory->create();
            $productScheduleModelCollection = $productScheduleModel->getCollection();
            $schedules = $params['staff'];

            /**
             * delete staff schedule
             */
            $staffSchedules = $staffScheduleModel->getCollection()->addFieldToFilter('staff_id', $userId);
            foreach ($staffSchedules as $staffSchedule) {
                $staffSchedule->delete();
            }
            $staffSchedules = $productScheduleModel->getCollection()->addFieldToFilter('staff_id', $userId);
            foreach ($staffSchedules as $staffSchedule) {
                $staffSchedule->delete();
            }

            /**
             * make staff schedule and save
             */
            $staffScheduleFinal = [];
            $newProductSchedules = [];
            foreach ($schedules as $schedule) {
                if ($schedule['product_id'] != null) {
                    $product = $productModel->getCollection()->addFieldToFilter('product_id', $schedule['product_id'])->getFirstItem();
                    if ($product->getId() && $product->getNeedStaff() == 1) {
                        if ($product->getOption() == $schedule['option']) {
                            if ($product->getOption() == 1) {
                                /**
                                 * check double schedule
                                 */
                                $_staffAdded = 0;
                                foreach ($staffScheduleFinal as $staffScheduleFinalItem) {
                                    if ($staffScheduleFinalItem['staff_id'] == $userId &&
                                        $staffScheduleFinalItem['weekday'] == $schedule['weekday'] &&
                                        $staffScheduleFinalItem['product_id'] == $schedule['product_id']
                                    ) {
                                        $_staffAdded = 1;
                                        break;
                                    }
                                }
                                if ($_staffAdded == 0) {
                                    array_push(
                                        $staffScheduleFinal,
                                        [
                                            'staff_id' => $userId,
                                            'product_id' => $schedule['product_id'],
                                            'staff_name' => $params['firstname'] . ' ' . $params['lastname'],
                                            'weekday' => $schedule['weekday'],
                                            'from_time' => "",
                                            'to_time' => ""
                                        ]
                                    );
                                    array_push(
                                        $newProductSchedules,
                                        [
                                            'product_id' => $schedule['product_id'],
                                            'staff_id' => $userId,
                                            'staff_name' => $params['firstname'] . ' ' . $params['lastname'],
                                            'weekday' => $schedule['weekday']
                                        ]
                                    );
                                } else {
                                    $this->_messageManager->addErrorMessage(__('Something went wrong in Work Schedule of user who has Id = ' . $userId));
                                }
                            } else {
                                if ($schedule['from_time'] != null && $schedule['to_time'] != null && strcmp($schedule['from_time'], $schedule['to_time']) < 0) {

                                    /**
                                     * check double schedule
                                     */
                                    $_staffAdded = 0;
                                    foreach ($staffScheduleFinal as $staffScheduleFinalItem) {
                                        if ($staffScheduleFinalItem['staff_id'] == $userId &&
                                            $staffScheduleFinalItem['weekday'] == $schedule['weekday'] &&
                                            $staffScheduleFinalItem['from_time'] == $schedule['from_time'] &&
                                            $staffScheduleFinalItem['to_time'] == $schedule['to_time'] &&
                                            $staffScheduleFinalItem['product_id'] == $schedule['product_id']
                                        ) {
                                            $_staffAdded = 1;
                                            break;
                                        }
                                        if ($staffScheduleFinalItem['weekday'] == $schedule['weekday']) {
                                            if (strcmp($schedule['from_time'], $staffScheduleFinalItem['from_time']) >= 0 && strcmp($schedule['from_time'], $staffScheduleFinalItem['to_time']) < 0) {
                                                $_staffAdded = 1;
                                                break;
                                            }
                                            if (strcmp($schedule['to_time'], $staffScheduleFinalItem['from_time']) > 0 && strcmp($schedule['to_time'], $staffScheduleFinalItem['to_time']) <= 0) {
                                                $_staffAdded = 1;
                                                break;
                                            }
                                        }
                                    }
                                    if ($_staffAdded == 0) {
                                        array_push(
                                            $staffScheduleFinal,
                                            [
                                                'staff_id' => $userId,
                                                'product_id' => $schedule['product_id'],
                                                'staff_name' => $params['firstname'] . ' ' . $params['lastname'],
                                                'weekday' => $schedule['weekday'],
                                                'from_time' => $schedule['from_time'],
                                                'to_time' => $schedule['to_time']
                                            ]
                                        );
                                        array_push(
                                            $newProductSchedules,
                                            [
                                                'product_id' => $schedule['product_id'],
                                                'staff_id' => $userId,
                                                'staff_name' => $params['firstname'] . ' ' . $params['lastname'],
                                                'weekday' => $schedule['weekday'],
                                                'from_time' => $schedule['from_time'],
                                                'to_time' => $schedule['to_time']
                                            ]
                                        );
                                    } else {
                                        $this->_messageManager->addErrorMessage(__('Something went wrong in Work Schedule of user who has Id = ' . $userId));
                                    }
                                } else {
                                    $this->_messageManager->addErrorMessage(__('Something went wrong in Work Schedule of user who has Id = ' . $userId));
                                }
                            }
                        } else {
                            $this->_messageManager->addErrorMessage(__('product ID : ' . $schedule['product_id'] . ' has wrong option in Work Schedule of user who has Id = ' . $userId . ', please retry.'));
                        }
                    } else {
                        $this->_messageManager->addErrorMessage(__('product ID : ' . $schedule['product_id'] . ' does not need staff in Work Schedule, please retry.'));
                    }
                }
            }

            foreach ($staffScheduleFinal as $staffScheduleFinalItem) {
                $staffScheduleModel->setData($staffScheduleFinalItem)->save();
            }

            /**
             * delete product schedule for this staff and insert all new
             */
            foreach ($schedules as $schedule) {
                if ($schedule['from_time'] != null && $schedule['to_time'] != null && $schedule['product_id'] != null) {
                    $oldProductSchedules = $productScheduleModelCollection->addFieldToFilter('staff_id', $userId);
                    foreach ($oldProductSchedules as $oldProductSchedule) {
                        $oldProductSchedule->delete();
                    }
                }
            }
            foreach ($newProductSchedules as $newProductSchedule) {
                $productScheduleModel->setData($newProductSchedule)->save();
            }
        }
    }
}
