<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWeeklyHighlightsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'week_start' => [
                'type' => 'DATE',
            ],
            'week_end' => [
                'type' => 'DATE',
            ],
            'likes_count' => [
                'type'     => 'INT',
                'default'  => 0,
            ],
            'rank' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['project_id', 'week_start']);
        $this->forge->addKey('week_start');
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('weekly_highlights');
    }

    public function down()
    {
        $this->forge->dropTable('weekly_highlights');
    }
}
