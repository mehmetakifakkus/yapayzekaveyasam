<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\BookmarkModel;
use App\Models\FollowModel;
use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    use ResponseTrait;

    protected ProjectModel $projectModel;
    protected LikeModel $likeModel;
    protected CommentModel $commentModel;
    protected BookmarkModel $bookmarkModel;
    protected FollowModel $followModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->projectModel = model('ProjectModel');
        $this->likeModel = model('LikeModel');
        $this->commentModel = model('CommentModel');
        $this->bookmarkModel = model('BookmarkModel');
        $this->followModel = model('FollowModel');
        $this->notificationModel = model('NotificationModel');
    }

    /**
     * Toggle like on a project
     */
    public function like(int $projectId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Beğenmek için giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        if ($this->isBanned()) {
            return $this->respond([
                'success' => false,
                'message' => 'Hesabınız askıya alınmıştır.',
            ], 403);
        }

        // Check if project exists
        $project = $this->projectModel->find($projectId);
        if (!$project) {
            return $this->respond([
                'success' => false,
                'message' => 'Proje bulunamadı.',
            ], 404);
        }

        $result = $this->likeModel->toggleLike($this->currentUser['id'], $projectId);

        // Create notification if liked (not for own project)
        if ($result['action'] === 'liked' && $project['user_id'] != $this->currentUser['id']) {
            $this->notificationModel->createNotification(
                $project['user_id'],
                $this->currentUser['id'],
                'like',
                $projectId
            );
        }

        return $this->respond([
            'success' => true,
            'action'  => $result['action'],
            'count'   => $result['count'],
            'message' => $result['action'] === 'liked' ? 'Beğendiniz!' : 'Beğeni kaldırıldı.',
        ]);
    }

    /**
     * Add comment to a project
     */
    public function comment()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Yorum yapmak için giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        if ($this->isBanned()) {
            return $this->respond([
                'success' => false,
                'message' => 'Hesabınız askıya alınmıştır.',
            ], 403);
        }

        $projectId = $this->request->getPost('project_id');
        $content = trim($this->request->getPost('content') ?? '');

        // Validate
        if (!$projectId) {
            return $this->respond([
                'success' => false,
                'message' => 'Proje ID gerekli.',
            ], 400);
        }

        if (strlen($content) < 2) {
            return $this->respond([
                'success' => false,
                'message' => 'Yorum en az 2 karakter olmalıdır.',
            ], 400);
        }

        if (strlen($content) > 1000) {
            return $this->respond([
                'success' => false,
                'message' => 'Yorum en fazla 1000 karakter olabilir.',
            ], 400);
        }

        // Check if project exists
        $project = $this->projectModel->find($projectId);
        if (!$project) {
            return $this->respond([
                'success' => false,
                'message' => 'Proje bulunamadı.',
            ], 404);
        }

        // Sanitize content
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        $comment = $this->commentModel->addComment(
            $this->currentUser['id'],
            (int) $projectId,
            $content
        );

        if (!$comment) {
            return $this->respond([
                'success' => false,
                'message' => 'Yorum eklenirken bir hata oluştu.',
            ], 500);
        }

        // Create notification (not for own project)
        if ($project['user_id'] != $this->currentUser['id']) {
            $this->notificationModel->createNotification(
                $project['user_id'],
                $this->currentUser['id'],
                'comment',
                (int) $projectId
            );
        }

        // Format date for display
        $comment['formatted_date'] = date('d M Y, H:i', strtotime($comment['created_at']));

        return $this->respond([
            'success' => true,
            'message' => 'Yorumunuz eklendi!',
            'comment' => $comment,
            'count'   => $this->commentModel->getCountByProject($projectId),
        ]);
    }

    /**
     * Delete a comment
     */
    public function deleteComment(int $commentId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Bu işlem için giriş yapmalısınız.',
            ], 401);
        }

        $deleted = $this->commentModel->deleteComment($commentId, $this->currentUser['id']);

        if (!$deleted) {
            return $this->respond([
                'success' => false,
                'message' => 'Yorum silinemedi.',
            ], 400);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Yorum silindi.',
        ]);
    }

    /**
     * Get projects (for infinite scroll or filtering)
     */
    public function projects()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'category' => $this->request->getGet('category'),
            'ai_tool'  => $this->request->getGet('ai_tool'),
            'sort'     => $this->request->getGet('sort') ?? 'newest',
            'search'   => $this->request->getGet('q'),
        ];

        $projects = $this->projectModel->getApprovedProjects($perPage, $offset, $filters);
        $totalProjects = $this->projectModel->getApprovedCount($filters);
        $totalPages = ceil($totalProjects / $perPage);

        // Enrich projects
        $aiToolModel = model('AiToolModel');
        foreach ($projects as &$project) {
            $project['likes_count'] = $this->likeModel->getCountByProject($project['id']);
            $project['ai_tools'] = $aiToolModel->getByProjectId($project['id']);
            $project['is_liked'] = $this->isLoggedIn()
                ? $this->likeModel->hasLiked($this->currentUser['id'], $project['id'])
                : false;
        }

        return $this->respond([
            'success'       => true,
            'projects'      => $projects,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalProjects' => $totalProjects,
            'hasMore'       => $page < $totalPages,
        ]);
    }

    /**
     * Toggle bookmark on a project
     */
    public function bookmark(int $projectId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Kaydetmek için giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        if ($this->isBanned()) {
            return $this->respond([
                'success' => false,
                'message' => 'Hesabınız askıya alınmıştır.',
            ], 403);
        }

        // Check if project exists
        $project = $this->projectModel->find($projectId);
        if (!$project) {
            return $this->respond([
                'success' => false,
                'message' => 'Proje bulunamadı.',
            ], 404);
        }

        $result = $this->bookmarkModel->toggleBookmark($this->currentUser['id'], $projectId);

        return $this->respond([
            'success'    => true,
            'action'     => $result['action'],
            'bookmarked' => $result['bookmarked'],
            'message'    => $result['bookmarked'] ? 'Kaydedildi!' : 'Kayıt kaldırıldı.',
        ]);
    }

    /**
     * Toggle follow on a user
     */
    public function follow(int $userId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Takip etmek için giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        if ($this->isBanned()) {
            return $this->respond([
                'success' => false,
                'message' => 'Hesabınız askıya alınmıştır.',
            ], 403);
        }

        // Check if user exists
        $userModel = model('UserModel');
        $targetUser = $userModel->find($userId);
        if (!$targetUser) {
            return $this->respond([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı.',
            ], 404);
        }

        $result = $this->followModel->toggleFollow($this->currentUser['id'], $userId);

        if ($result['action'] === 'error') {
            return $this->respond([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        // Create notification if followed (not for self)
        if ($result['action'] === 'followed') {
            $this->notificationModel->createNotification(
                $userId,
                $this->currentUser['id'],
                'follow',
                null
            );
        }

        return $this->respond([
            'success'   => true,
            'action'    => $result['action'],
            'following' => $result['following'],
            'message'   => $result['following'] ? 'Takip ediyorsunuz!' : 'Takibi bıraktınız.',
        ]);
    }
}
