<?php

namespace App\Models;

use CodeIgniter\Model;

class FollowModel extends Model
{
    protected $table            = 'follows';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'follower_id',
        'following_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Toggle follow
     */
    public function toggleFollow(int $followerId, int $followingId): array
    {
        // Can't follow yourself
        if ($followerId === $followingId) {
            return ['action' => 'error', 'message' => 'Kendinizi takip edemezsiniz.'];
        }

        $existing = $this->where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'unfollowed', 'following' => false];
        }

        $this->insert([
            'follower_id'  => $followerId,
            'following_id' => $followingId,
        ]);

        return ['action' => 'followed', 'following' => true];
    }

    /**
     * Check if user is following another user
     */
    public function isFollowing(int $followerId, int $followingId): bool
    {
        return $this->where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->countAllResults() > 0;
    }

    /**
     * Get follower count for user
     */
    public function getFollowerCount(int $userId): int
    {
        return $this->where('following_id', $userId)->countAllResults();
    }

    /**
     * Get following count for user
     */
    public function getFollowingCount(int $userId): int
    {
        return $this->where('follower_id', $userId)->countAllResults();
    }

    /**
     * Get followers of a user
     */
    public function getFollowers(int $userId, int $limit = 20, int $offset = 0): array
    {
        return $this->select('users.id, users.name, users.avatar, users.bio')
            ->join('users', 'users.id = follows.follower_id')
            ->where('follows.following_id', $userId)
            ->orderBy('follows.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get users that a user is following
     */
    public function getFollowing(int $userId, int $limit = 20, int $offset = 0): array
    {
        return $this->select('users.id, users.name, users.avatar, users.bio')
            ->join('users', 'users.id = follows.following_id')
            ->where('follows.follower_id', $userId)
            ->orderBy('follows.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get followed users' IDs
     */
    public function getFollowingIds(int $userId): array
    {
        $results = $this->select('following_id')
            ->where('follower_id', $userId)
            ->findAll();

        // Return as array of integers (cast to ensure proper type)
        return array_map('intval', array_column($results, 'following_id'));
    }
}
