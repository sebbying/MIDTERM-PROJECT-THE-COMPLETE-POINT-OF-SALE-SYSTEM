<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosTables extends Migration
{
    public function up()
    {
        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'name' => ['type' => 'VARCHAR', 'constraint' => 100], 'price' => ['type' => 'DECIMAL', 'constraint' => '10,2'], 'stock_quantity' => ['type' => 'INT', 'default' => 0], 'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], 'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1], 'created_at' => ['type' => 'DATETIME']]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'full_name' => ['type' => 'VARCHAR', 'constraint' => 100], 'email' => ['type' => 'VARCHAR', 'constraint' => 100], 'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], 'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1], 'created_at' => ['type' => 'DATETIME']]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'username' => ['type' => 'VARCHAR', 'constraint' => 50], 'full_name' => ['type' => 'VARCHAR', 'constraint' => 100], 'password' => ['type' => 'VARCHAR', 'constraint' => 255], 'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], 'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1], 'created_at' => ['type' => 'DATETIME']]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'product_id' => ['type' => 'INT', 'unsigned' => true], 'customer_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true], 'sold_by' => ['type' => 'INT', 'unsigned' => true], 'quantity' => ['type' => 'INT'], 'total_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'], 'created_at' => ['type' => 'DATETIME']]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('sold_by', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('sales', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        foreach (['sales', 'users', 'customers', 'products'] as $table) $this->forge->dropTable($table, true);
    }
}
