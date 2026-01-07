<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentLikeModel extends Model
{
    protected $table            = 'comment_likes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'comment_id',
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
    public function toggleLike(int $userId, int $commentId): array
    {
        $existing = $this->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'unliked', 'count' => $this->getCountByComment($commentId)];
        }

        $this->insert([
            'user_id'    => $userId,
            'comment_id' => $commentId,
        ]);

        return ['action' => 'liked', 'count' => $this->getCountByComment($commentId)];
    }

    /**
     * Check if user liked a comment
     */
    public function hasLiked(int $userId, int $commentId): bool
    {
        return $this->where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->countAllResults() > 0;
    }

    /**
     * Get like count for a comment
     */
    public function getCountByComment(int $commentId): int
    {
        return $this->where('comment_id', $commentId)->countAllResults();
    }

    /**
     * Get likes count for multiple comments (batch)
     */
    public function getCountsForComments(array $commentIds): array
    {
        if (empty($commentIds)) {
            return [];
        }

        $results = $this->select('comment_id, COUNT(*) as count')
            ->whereIn('comment_id', $commentIds)
            ->groupBy('comment_id')
            ->findAll();

        $counts = [];
        foreach ($results as $row) {
            $counts[$row['comment_id']] = (int) $row['count'];
        }

        return $counts;
    }

    /**
     * Get liked comment IDs by user (batch check)
     */
    public function getLikedCommentIds(int $userId, array $commentIds): array
    {
        if (empty($commentIds)) {
            return [];
        }

        return array_column(
            $this->select('comment_id')
                ->where('user_id', $userId)
                ->whereIn('comment_id', $commentIds)
                ->findAll(),
            'comment_id'
        );
    }
}
