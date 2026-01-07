<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserBadgesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'badge_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'earned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'badge_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('badge_id', 'badges', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_badges');
    }

    public function down()
    {
        $this->forge->dropTable('user_badges');
    }
}
