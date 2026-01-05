<?php

namespace App\Models;

use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $table            = 'bookmarks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'project_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Toggle bookmark
     */
    public function toggleBookmark(int $userId, int $projectId): array
    {
        $existing = $this->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'removed', 'bookmarked' => false];
        }

        $this->insert([
            'user_id'    => $userId,
            'project_id' => $projectId,
        ]);

        return ['action' => 'added', 'bookmarked' => true];
    }

    /**
     * Check if user has bookmarked a project
     */
    public function hasBookmarked(int $userId, int $projectId): bool
    {
        return $this->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->countAllResults() > 0;
    }

    /**
     * Get bookmarked projects for user
     */
    public function getBookmarkedProjects(int $userId, int $limit = 12, int $offset = 0): array
    {
        return $this->select('projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name, categories.slug as category_slug')
            ->select('(SELECT COUNT(*) FROM likes WHERE likes.project_id = projects.id) as likes_count', false)
            ->join('projects', 'projects.id = bookmarks.project_id')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('bookmarks.user_id', $userId)
            ->where('projects.status', 'approved')
            ->orderBy('bookmarks.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get bookmark count for user
     */
    public function getCountForUser(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }
}
