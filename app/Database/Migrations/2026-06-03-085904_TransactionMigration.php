<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TransactionMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['income', 'expenses'],
            ],

            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],

            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'transaction_date' => [
                'type' => 'DATE',
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // PRIMARY KEY
        $this->forge->addKey('id', true);

        // INDEXES (VERY IMPORTANT for performance + security queries)
        $this->forge->addKey('user_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey(['user_id', 'transaction_date']);

        // FOREIGN KEYS (DATA INTEGRITY)
        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'category_id',
            'categories',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('transactions', true);
    }
}