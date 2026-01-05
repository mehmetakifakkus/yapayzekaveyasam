<?php

namespace App\Models;

use CodeIgniter\Model;

class LikeModel extends Model
{
    protected $table            = 'likes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'project_id',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Toggle like (add or remove)
     */
    public function toggleLike(int $userId, int $projectId): array
    {
        $existing = $this->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'unliked', 'count' => $this->getCountByProject($projectId)];
        }

        $this->insert([
            'user_id'    => $userId,
            'project_id' => $projectId,
        ]);

        return ['action' => 'liked', 'count' => $this->getCountByProject($projectId)];
    }

    /**
     * Check if user liked a project
     */
    public function hasLiked(int $userId, int $projectId): bool
    {
        return $this->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->countAllResults() > 0;
    }

    /**
     * Get like count for a project
     */
    public function getCountByProject(int $projectId): int
    {
        return $this->where('project_id', $projectId)->countAllResults();
    }

    /**
     * Get user IDs who liked a project
     */
    public function getUserIdsByProject(int $projectId): array
    {
        return array_column(
            $this->select('user_id')
                ->where('project_id', $projectId)
                ->findAll(),
            'user_id'
        );
    }
}
