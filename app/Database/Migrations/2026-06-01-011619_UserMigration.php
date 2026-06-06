<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],

            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],

            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],

            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true
            ],

            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'unique' => true
            ],

            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],


            'email_verified_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'email_verification_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],

            'reset_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],

            'reset_token_expires' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],

            'failed_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],

            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true
            ],

            // ⏱ timestamps
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}