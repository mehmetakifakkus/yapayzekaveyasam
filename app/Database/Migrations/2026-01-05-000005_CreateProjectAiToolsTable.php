<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectAiToolsTable extends Migration
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
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'ai_tool_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'ai_tool_id']);
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ai_tool_id', 'ai_tools', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('project_ai_tools');
    }

    public function down()
    {
        $this->forge->dropTable('project_ai_tools');
    }
}
