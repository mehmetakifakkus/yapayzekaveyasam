<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'project_id',
        'content',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'content' => 'required|min_length[2]|max_length[1000]',
    ];

    /**
     * Get comments for a project with user info
     */
    public function getByProjectId(int $projectId, int $limit = 50): array
    {
        return $this->select('comments.*, users.name as user_name, users.avatar as user_avatar')
            ->join('users', 'users.id = comments.user_id')
            ->where('comments.project_id', $projectId)
            ->orderBy('comments.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get comment count for a project
     */
    public function getCountByProject(int $projectId): int
    {
        return $this->where('project_id', $projectId)->countAllResults();
    }

    /**
     * Add a comment
     */
    public function addComment(int $userId, int $projectId, string $content): array|bool
    {
        $data = [
            'user_id'    => $userId,
            'project_id' => $projectId,
            'content'    => $content,
        ];

        if ($this->insert($data)) {
            $commentId = $this->getInsertID();
            return $this->select('comments.*, users.name as user_name, users.avatar as user_avatar')
                ->join('users', 'users.id = comments.user_id')
                ->where('comments.id', $commentId)
                ->first();
        }

        return false;
    }

    /**
     * Delete comment (only owner can delete)
     */
    public function deleteComment(int $commentId, int $userId): bool
    {
        return $this->where('id', $commentId)
            ->where('user_id', $userId)
            ->delete();
    }
}
