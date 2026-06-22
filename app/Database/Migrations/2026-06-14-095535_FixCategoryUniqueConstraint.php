<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCategoryUniqueConstraint extends Migration
{
    public function up()
    {
        // Safely drop the old index only if it exists
        if ($this->indexExists('categories', 'category_name')) {
            $this->forge->dropKey('categories', 'category_name', true);
        }

        $this->forge->addUniqueKey(['user_id', 'category_name']);
        $this->forge->processIndexes('categories');
    }

    public function down()
    {
        if ($this->indexExists('categories', 'user_id_category_name')) {
            $this->forge->dropKey('categories', 'user_id_category_name', true);
        }

      
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $query = $this->db->query(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return $query->getNumRows() > 0;
    }
}
