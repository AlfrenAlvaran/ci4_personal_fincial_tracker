<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Categorie extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true,],
            'user_id' => ['type' => 'TINYINT', 'null' => false],
            'category_name' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'category_type' => ['type' => 'ENUM', 'constraint' => ['expenses', 'income'], 'default' => 'expenses'],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 15],
            'note' => ['type' => 'TEXT', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('categories');
    }

    public function down()
    {
        $this->forge->dropTable('categories', true);
    }
}
