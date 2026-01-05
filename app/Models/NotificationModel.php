<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'actor_id',
        'type',
        'project_id',
        'content',
        'is_read',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get notifications for user with actor and project info
     */
    public function getForUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        return $this->select('notifications.*,
                             actor.name as actor_name, actor.avatar as actor_avatar,
                             projects.title as project_title, projects.slug as project_slug')
            ->join('users as actor', 'actor.id = notifications.actor_id', 'left')
            ->join('projects', 'projects.id = notifications.project_id', 'left')
            ->where('notifications.user_id', $userId)
            ->orderBy('notifications.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        return $this->where('id', $notificationId)
            ->where('user_id', $userId)
            ->set('is_read', 1)
            ->update();
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->set('is_read', 1)
            ->update();
    }

    /**
     * Create notification
     */
    public function createNotification(int $userId, string $type, ?int $actorId = null, ?int $projectId = null, ?string $content = null): int|false
    {
        // Don't notify self
        if ($actorId && $actorId === $userId) {
            return false;
        }

        return $this->insert([
            'user_id'    => $userId,
            'actor_id'   => $actorId,
            'type'       => $type,
            'project_id' => $projectId,
            'content'    => $content,
        ]);
    }

    /**
     * Delete old notifications (older than 30 days)
     */
    public function cleanupOld(): int
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->delete();
    }
}
