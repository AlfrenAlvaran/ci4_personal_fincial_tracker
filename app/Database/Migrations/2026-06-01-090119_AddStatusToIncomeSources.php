<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToIncomeSources extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('status', 'income_sources')) {
            $this->forge->addColumn('income_sources', [
                'status' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'after' => 'image'
                ]
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('income_sources', 'status');
    }
}
