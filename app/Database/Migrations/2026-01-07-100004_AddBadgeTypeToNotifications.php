<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBadgeTypeToNotifications extends Migration
{
    public function up()
    {
        // Modify the type enum to include 'badge'
        $this->db->query("ALTER TABLE notifications MODIFY COLUMN type ENUM('like', 'comment', 'follow', 'project_approved', 'project_rejected', 'badge') NOT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE notifications MODIFY COLUMN type ENUM('like', 'comment', 'follow', 'project_approved', 'project_rejected') NOT NULL");
    }
}
