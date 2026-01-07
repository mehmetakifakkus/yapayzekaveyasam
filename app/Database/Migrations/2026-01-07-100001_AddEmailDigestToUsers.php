<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailDigestToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'email_digest' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'theme',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'email_digest');
    }
}
