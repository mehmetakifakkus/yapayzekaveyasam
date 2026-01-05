<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionReasonToProjects extends Migration
{
    public function up()
    {
        $this->forge->addColumn('projects', [
            'rejection_reason' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('projects', 'rejection_reason');
    }
}
