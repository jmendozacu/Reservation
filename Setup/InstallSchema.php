<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:14
 */
namespace Magenest\Reservation\Setup;

use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magenest\Reservation\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /**
         * insert new table
         */
        $installer = $setup;
        /**
         * Create table 'magenest_reservation_product_schedule'
         */
        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_product_schedule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )
            ->addColumn(
                'staff_id',
                Table::TYPE_INTEGER,
                null,
                [],
                'Staff ID'
            )
            ->addColumn(
                'staff_name',
                Table::TYPE_TEXT,
                255,
                [],
                'Staff Name'
            )
            ->addColumn(
                'weekday',
                Table::TYPE_DECIMAL,
                3,
                [],
                'weekday'
            )
            ->addColumn(
                'from_time',
                Table::TYPE_TEXT,
                10,
                [],
                'From'
            )
            ->addColumn(
                'to_time',
                Table::TYPE_TEXT,
                10,
                [],
                'To'
            )
            ->addColumn(
                'orders',
                Table::TYPE_TEXT,
                null,
                [],
                'Orders'
            )
            ->addColumn(
                'date_created',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Date created'
            )
            ->setComment('Reservation schedule table with staff');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_product_schedule_without_staff'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )
            ->addColumn(
                'weekday',
                Table::TYPE_DECIMAL,
                3,
                [],
                'weekday'
            )
            ->addColumn(
                'from_time',
                Table::TYPE_TEXT,
                10,
                [],
                'From'
            )
            ->addColumn(
                'to_time',
                Table::TYPE_TEXT,
                10,
                [],
                'To'
            )
            ->addColumn(
                'orders',
                Table::TYPE_TEXT,
                null,
                [],
                'Orders'
            )
            ->addColumn(
                'slots',
                Table::TYPE_INTEGER,
                null,
                [],
                'Slots'
            )
            ->addColumn(
                'date_created',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Date created'
            )
            ->setComment('Reservation schedule table without staff');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_staff_schedule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'staff_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Staff ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product ID'
            )
            ->addColumn(
                'staff_name',
                Table::TYPE_TEXT,
                255,
                [],
                'Staff Name'
            )
            ->addColumn(
                'weekday',
                Table::TYPE_INTEGER,
                null,
                [],
                'From'
            )->addColumn(
                'from_time',
                Table::TYPE_TEXT,
                10,
                [],
                'To'
            )->addColumn(
                'to_time',
                Table::TYPE_TEXT,
                10,
                [],
                'To'
            )
            ->setComment('Staff schedule table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_staff'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'staff_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Staff ID'
            )
            ->addColumn(
                'staff_name',
                Table::TYPE_TEXT,
                255,
                [],
                'Staff First Name'
            )
            ->addColumn(
                'staff_avatar',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Avatar Description'
            )
            ->addColumn(
                'staff_intro',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Staff Introduction'
            )
            ->addColumn(
                'staff_type',
                Table::TYPE_TEXT,
                255,
                [],
                'Staff Type'
            )
            ->setComment('Staff information table');

        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_order'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Order ID'
            )
            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Order item ID'
            )
            ->addColumn(
                'order_item_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Order item name'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer ID'
            )
            ->addColumn(
                'customer_email',
                Table::TYPE_TEXT,
                null,
                [],
                'Customer email'
            )
            ->addColumn(
                'customer_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Customer name'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                null,
                [],
                'Status'
            )
            ->addColumn(
                'date',
                Table::TYPE_TEXT,
                null,
                [],
                'Reservation Date'
            )
            ->addColumn(
                'special_date',
                Table::TYPE_TEXT,
                null,
                [],
                'Special Date'
            )
            ->addColumn(
                'from_time',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Start time'
            )
            ->addColumn(
                'to_time',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'End time'
            )
            ->addColumn(
                'slots',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'End time'
            )
            ->addColumn(
                'user_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Staff ID'
            )
            ->addColumn(
                'user_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Staff name'
            )
            ->addColumn(
                'reservation_status',
                Table::TYPE_TEXT,
                null,
                [],
                'Schedule status'
            )
            ->addColumn(
                'cancel_request',
                Table::TYPE_INTEGER,
                null,
                [],
                'Cancel Request Status'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Date created'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Date updated'
            )
            ->setComment('Product orders table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_staff_price_rule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'rule_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Rule Name'
            )
            ->addColumn(
                'rule_amount',
                Table::TYPE_DECIMAL,
                '10,2',
                [],
                'Rule Amount'
            )
            ->setComment('Price Rules For Staff');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_special_date'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'date_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Date Name'
            )
            ->addColumn(
                'date_from',
                Table::TYPE_TEXT,
                null,
                [],
                'Special Date From'
            )
            ->addColumn(
                'date_to',
                Table::TYPE_TEXT,
                null,
                [],
                'Special Date To'
            )
            ->addColumn(
                'date_option',
                Table::TYPE_TEXT,
                null,
                [],
                'Date Option'
            )
            ->addColumn(
                'date_amount',
                Table::TYPE_DECIMAL,
                '10,2',
                [],
                'Date Amount'
            )
            ->setComment('Special Date');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_product_price_rule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'rule_option',
                Table::TYPE_INTEGER,
                null,
                [],
                'Rule Option'
            )
            ->addColumn(
                'rule_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Rule Name'
            )
            ->addColumn(
                'rule_from',
                Table::TYPE_TEXT,
                null,
                [],
                'Rule From'
            )
            ->addColumn(
                'rule_to',
                Table::TYPE_TEXT,
                null,
                [],
                'Rule To'
            )
            ->addColumn(
                'rule_function',
                Table::TYPE_INTEGER,
                null,
                [],
                'Rule Function'
            )
            ->addColumn(
                'rule_unit',
                Table::TYPE_INTEGER,
                null,
                [],
                'Rule Unit'
            )
            ->addColumn(
                'rule_amount',
                Table::TYPE_DECIMAL,
                '10,2',
                [],
                'Rule Amount'
            )
            ->setComment('Product Price Rules');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_cancel_request'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Order ID'
            )
            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Order item ID'
            )
            ->addColumn(
                'order_item_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Order item name'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer ID'
            )
            ->addColumn(
                'customer_email',
                Table::TYPE_TEXT,
                null,
                [],
                'Customer email'
            )
            ->addColumn(
                'customer_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Customer name'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                null,
                [],
                'Status'
            )
            ->addColumn(
                'date',
                Table::TYPE_TEXT,
                null,
                [],
                'Reservation Date'
            )
            ->addColumn(
                'from_time',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Start time'
            )
            ->addColumn(
                'to_time',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'End time'
            )
            ->addColumn(
                'slots',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Slots'
            )
            ->addColumn(
                'user_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Staff ID'
            )
            ->addColumn(
                'user_name',
                Table::TYPE_TEXT,
                null,
                [],
                'Staff name'
            )
            ->addColumn(
                'reservation_status',
                Table::TYPE_TEXT,
                null,
                [],
                'Schedule status'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Date created'
            )
            ->setComment('Cancel Request From Customer Table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_product'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product ID'
            )
            ->addColumn(
                'need_staff',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Need Staff Or Not'
            )
            ->addColumn(
                'option',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Reservation Option'
            )
            ->setComment('Reservation Product Table');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('magenest_reservation_user'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'session_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Session Id'
            )
            ->addColumn(
                'staff_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Staff Id'
            )
            ->setComment('Reservation Session Table');

        $installer->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
