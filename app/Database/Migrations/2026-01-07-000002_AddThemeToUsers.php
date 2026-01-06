<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddThemeToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'theme' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'default',
                'after' => 'bio',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'theme');
    }
}
