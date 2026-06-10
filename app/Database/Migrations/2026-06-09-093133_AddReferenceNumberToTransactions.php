<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReferenceNumberToTransactions extends Migration
{
    public function up()
    {

        if (! $this->db->fieldExists('reference_number', 'transactions')) {
            $this->forge->addColumn('transactions', [
                'reference_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => false,
                ],
            ]);
        }

        $this->db->query("ALTER TABLE transactions ADD UNIQUE (reference_number)");
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'reference_number');
    }
}
