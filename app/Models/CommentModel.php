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
        'parent_id',
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
     * Get comments for a project with user info (nested structure)
     */
    public function getByProjectId(int $projectId, int $limit = 100, ?int $userId = null): array
    {
        $comments = $this->select('comments.*, users.name as user_name, users.avatar as user_avatar')
            ->select('(SELECT COUNT(*) FROM comment_likes WHERE comment_likes.comment_id = comments.id) as likes_count', false)
            ->join('users', 'users.id = comments.user_id')
            ->where('comments.project_id', $projectId)
            ->orderBy('comments.created_at', 'ASC')
            ->limit($limit)
            ->findAll();

        // Get liked comment IDs for current user
        $likedIds = [];
        if ($userId && !empty($comments)) {
            $commentLikeModel = model('CommentLikeModel');
            $commentIds = array_column($comments, 'id');
            $likedIds = $commentLikeModel->getLikedCommentIds($userId, $commentIds);
        }

        // Add is_liked to each comment
        foreach ($comments as &$comment) {
            $comment['is_liked'] = in_array($comment['id'], $likedIds);
        }

        return $this->buildTree($comments);
    }

    /**
     * Build nested comment tree
     */
    private function buildTree(array $comments): array
    {
        $indexed = [];
        foreach ($comments as &$comment) {
            $comment['replies'] = [];
            $indexed[$comment['id']] = &$comment;
        }

        $tree = [];
        foreach ($comments as &$comment) {
            if ($comment['parent_id'] && isset($indexed[$comment['parent_id']])) {
                $indexed[$comment['parent_id']]['replies'][] = &$comment;
            } else {
                $tree[] = &$comment;
            }
        }

        // Reverse to show newest first for top-level
        return array_reverse($tree);
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
    public function addComment(int $userId, int $projectId, string $content, ?int $parentId = null): array|bool
    {
        $data = [
            'user_id'    => $userId,
            'project_id' => $projectId,
            'content'    => $content,
            'parent_id'  => $parentId,
        ];

        if ($this->insert($data)) {
            $commentId = $this->getInsertID();
            $comment = $this->select('comments.*, users.name as user_name, users.avatar as user_avatar')
                ->join('users', 'users.id = comments.user_id')
                ->where('comments.id', $commentId)
                ->first();
            $comment['replies'] = [];
            return $comment;
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
