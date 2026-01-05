<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFollowsTable extends Migration
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
            'follower_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'following_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['follower_id', 'following_id']);
        $this->forge->addKey('follower_id');
        $this->forge->addKey('following_id');
        $this->forge->addForeignKey('follower_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('following_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('follows');
    }

    public function down()
    {
        $this->forge->dropTable('follows');
    }
}
