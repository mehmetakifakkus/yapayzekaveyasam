<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToComments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comments', [
            'parent_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ],
        ]);

        // Add foreign key
        $this->db->query('ALTER TABLE comments ADD CONSTRAINT fk_comment_parent FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE comments DROP FOREIGN KEY fk_comment_parent');
        $this->forge->dropColumn('comments', 'parent_id');
    }
}
