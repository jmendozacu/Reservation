<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:12
 */
namespace Magenest\Reservation\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
//        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY,"magenest_reservation_type");

        // add attributes for product
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'magenest_reservation_type',
            [
                'group' => 'Magenest Booking And Reservation',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'label' => 'Magenest Reservation Product ',
                'input' => 'boolean',
                'sort_order' => 60,
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'used_in_product_listing' => true,
                'apply_to' => 'simple,configurable,virtual,bundle,grouped,downloadable'
            ]
        );
    }
}
