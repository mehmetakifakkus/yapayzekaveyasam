<?php

namespace App\Libraries;

use App\Models\BadgeModel;
use App\Models\UserBadgeModel;
use App\Models\UserModel;
use App\Models\FollowModel;
use App\Models\NotificationModel;

class BadgeChecker
{
    protected BadgeModel $badgeModel;
    protected UserBadgeModel $userBadgeModel;
    protected UserModel $userModel;
    protected FollowModel $followModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->badgeModel = model('BadgeModel');
        $this->userBadgeModel = model('UserBadgeModel');
        $this->userModel = model('UserModel');
        $this->followModel = model('FollowModel');
        $this->notificationModel = model('NotificationModel');
    }

    /**
     * Check and award project-related badges
     * Called when a project is approved
     */
    public function checkProjectBadges(int $userId): array
    {
        $awarded = [];
        $projectCount = $this->userModel->getProjectsCount($userId);

        // First project badge
        if ($projectCount >= 1) {
            $badge = $this->badgeModel->findBySlug('first_project');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        // 5 projects badge
        if ($projectCount >= 5) {
            $badge = $this->badgeModel->findBySlug('projects_5');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        // 10 projects badge
        if ($projectCount >= 10) {
            $badge = $this->badgeModel->findBySlug('projects_10');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        return $awarded;
    }

    /**
     * Check and award like-related badges
     * Called when a project receives a like
     */
    public function checkLikeBadges(int $userId): array
    {
        $awarded = [];
        $totalLikes = $this->userModel->getTotalLikesReceived($userId);

        // 10 likes badge
        if ($totalLikes >= 10) {
            $badge = $this->badgeModel->findBySlug('likes_10');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        // 50 likes badge
        if ($totalLikes >= 50) {
            $badge = $this->badgeModel->findBySlug('likes_50');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        // 100 likes badge
        if ($totalLikes >= 100) {
            $badge = $this->badgeModel->findBySlug('likes_100');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        return $awarded;
    }

    /**
     * Check and award follower-related badges
     * Called when a user gets a new follower
     */
    public function checkFollowerBadges(int $userId): array
    {
        $awarded = [];
        $followerCount = $this->followModel->getFollowerCount($userId);

        // 10 followers badge
        if ($followerCount >= 10) {
            $badge = $this->badgeModel->findBySlug('followers_10');
            if ($badge && $this->awardBadge($userId, $badge)) {
                $awarded[] = $badge;
            }
        }

        return $awarded;
    }

    /**
     * Award a badge to user and create notification
     */
    protected function awardBadge(int $userId, array $badge): bool
    {
        // Check if user already has this badge
        if ($this->userBadgeModel->hasBadge($userId, $badge['id'])) {
            return false;
        }

        // Award the badge
        $result = $this->userBadgeModel->awardBadge($userId, $badge['id']);

        if ($result) {
            // Create notification for badge
            $this->notificationModel->createNotification(
                $userId,
                'badge',
                null,
                null,
                $badge['icon'] . ' ' . $badge['name'] . ' rozetini kazandınız!'
            );
        }

        return $result;
    }

    /**
     * Check all badges for a user (useful for retroactive badge assignment)
     */
    public function checkAllBadges(int $userId): array
    {
        $awarded = [];
        $awarded = array_merge($awarded, $this->checkProjectBadges($userId));
        $awarded = array_merge($awarded, $this->checkLikeBadges($userId));
        $awarded = array_merge($awarded, $this->checkFollowerBadges($userId));
        return $awarded;
    }
}
