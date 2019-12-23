<?php
namespace Magenest\Reservation\Setup;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Store\Model\StoreManagerInterface;
class UpgradeData implements UpgradeDataInterface
{
    protected $EavSetup;
    protected $config;
    protected $storeManager;
    protected $attributeResource;
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        StoreManagerInterface $storeManager,
        Config $eavConfig,
        Attribute $attributeResource
    )
    {
        $this->attributeResource = $attributeResource;
        $this->config = $eavConfig;
        $this->EavSetup = $eavSetupFactory;
        $this->storeManager = $storeManager;
    }
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if ( version_compare($context->getVersion(), '1.0.3', '<' ))
        {
            $eavSetup = $this->EavSetup->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, "wedding_id,");
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'wedding_id',
                [
                    'group' => 'New Group Dat Dat',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'wedding_id',
                    'input' => 'text',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                ]
            );
        }
        if ( version_compare($context->getVersion(), '1.0.4', '<' ))
        {
            $eavSetup = $this->EavSetup->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, "Test,");
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'Test',
                [
                    'group' => 'New Group Dat Dat',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Test',
                    'input' => 'text',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                ]
            );
        }
        if ( version_compare($context->getVersion(), '1.0.5', '<' ))
        {
            $eavSetup = $this->EavSetup->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, "Test2,");
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'Test2',
                [
                    'group' => 'Customizable Options',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Test',
                    'input' => 'text',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                ]
            );
        }
    }
}