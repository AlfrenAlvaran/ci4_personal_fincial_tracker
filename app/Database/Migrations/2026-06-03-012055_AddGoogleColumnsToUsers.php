<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGoogleColumnsToUsers extends Migration
{
    public function up()
    {
        $fields = [

            'google_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'email'
            ],

            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'local'
            ],

            'avatar' => [
                'type' => 'TEXT',
                'null' => true
            ],

            'remember_token' => [
                'type' => 'TEXT',
                'null' => true
            ],

            // 'email_verified_at' => [
            //     'type' => 'DATETIME',
            //     'null' => true
            // ],

            // 'reset_token' => [
            //     'type' => 'TEXT',
            //     'null' => true
            // ],

            // 'reset_token_expires' => [
            //     'type' => 'DATETIME',
            //     'null' => true
            // ]
        ];

        $this->forge->addColumn(
            'users',
            $fields
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            'users',
            [
                'google_id',
                'provider',
                'avatar',
                'remember_token',
                // 'email_verified_at',
                // 'reset_token',
                // 'reset_token_expires'
            ]
        );
    }
}