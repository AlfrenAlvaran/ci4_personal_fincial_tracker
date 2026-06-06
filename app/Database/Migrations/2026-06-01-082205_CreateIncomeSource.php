<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncomeSource extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],

            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],

            'monthly_average' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'status'=>[
                'type' => 'TINYINT',
                'constraint'=>1,
                'default'=>1
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
        $this->forge->addKey('id', true);

        $this->forge->createTable('income_sources');
    }

    public function down()
    {
        $this->forge->dropTable('income_sources');
    }
}
