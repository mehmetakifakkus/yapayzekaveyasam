<?php

namespace App\Models;

use CodeIgniter\Model;

class UserBadgeModel extends Model
{
    protected $table            = 'user_badges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'badge_id',
        'earned_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;

    /**
     * Get all badges for a user
     */
    public function getUserBadges(int $userId): array
    {
        return $this->select('badges.*, user_badges.earned_at')
            ->join('badges', 'badges.id = user_badges.badge_id')
            ->where('user_badges.user_id', $userId)
            ->orderBy('user_badges.earned_at', 'DESC')
            ->findAll();
    }

    /**
     * Check if user has a specific badge
     */
    public function hasBadge(int $userId, int $badgeId): bool
    {
        return $this->where('user_id', $userId)
            ->where('badge_id', $badgeId)
            ->countAllResults() > 0;
    }

    /**
     * Check if user has badge by slug
     */
    public function hasBadgeBySlug(int $userId, string $slug): bool
    {
        return $this->select('user_badges.id')
            ->join('badges', 'badges.id = user_badges.badge_id')
            ->where('user_badges.user_id', $userId)
            ->where('badges.slug', $slug)
            ->countAllResults() > 0;
    }

    /**
     * Award a badge to user
     */
    public function awardBadge(int $userId, int $badgeId): bool
    {
        // Check if already has badge
        if ($this->hasBadge($userId, $badgeId)) {
            return false;
        }

        return $this->insert([
            'user_id'   => $userId,
            'badge_id'  => $badgeId,
            'earned_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get badge count for user
     */
    public function getBadgeCount(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    /**
     * Get recently earned badges (for notifications)
     */
    public function getRecentBadges(int $userId, int $limit = 5): array
    {
        return $this->select('badges.*, user_badges.earned_at')
            ->join('badges', 'badges.id = user_badges.badge_id')
            ->where('user_badges.user_id', $userId)
            ->orderBy('user_badges.earned_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
