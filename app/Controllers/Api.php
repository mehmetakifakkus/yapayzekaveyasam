<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\CommentLikeModel;
use App\Models\BookmarkModel;
use App\Models\FollowModel;
use App\Models\NotificationModel;
use App\Models\ConversationModel;
use App\Models\MessageModel;
use App\Libraries\BadgeChecker;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    use ResponseTrait;

    protected ProjectModel $projectModel;
    protected LikeModel $likeModel;
    protected CommentModel $commentModel;
    protected CommentLikeModel $commentLikeModel;
    protected BookmarkModel $bookmarkModel;
    protected FollowModel $followModel;
    protected NotificationModel $notificationModel;
    protected ConversationModel $conversationModel;
    protected MessageModel $messageModel;

    public function __construct()
    {
        $this->projectModel = model('ProjectModel');
        $this->likeModel = model('LikeModel');
        $this->commentModel = model('CommentModel');
        $this->commentLikeModel = model('CommentLikeModel');
        $this->bookmarkModel = model('BookmarkModel');
        $this->followModel = model('FollowModel');
        $this->notificationModel = model('NotificationModel');
        $this->conversationModel = model('ConversationModel');
        $this->messageModel = model('MessageModel');
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
                'like',
                $this->currentUser['id'],
                $projectId
            );

            // Check for like badges for project owner
            $badgeChecker = new BadgeChecker();
            $badgeChecker->checkLikeBadges((int) $project['user_id']);
        }

        return $this->respond([
            'success' => true,
            'action'  => $result['action'],
            'count'   => $result['count'],
            'message' => $result['action'] === 'liked' ? 'Beğendiniz!' : 'Beğeni kaldırıldı.',
        ]);
    }

    /**
     * Toggle like on a comment
     */
    public function likeComment(int $commentId)
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

        // Check if comment exists
        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            return $this->respond([
                'success' => false,
                'message' => 'Yorum bulunamadı.',
            ], 404);
        }

        $result = $this->commentLikeModel->toggleLike($this->currentUser['id'], $commentId);

        // Create notification if liked (not for own comment)
        if ($result['action'] === 'liked' && $comment['user_id'] != $this->currentUser['id']) {
            $this->notificationModel->createNotification(
                $comment['user_id'],
                'comment_like',
                $this->currentUser['id'],
                $comment['project_id'],
                'Yorumunuzu beğendi'
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
        $parentId = $this->request->getPost('parent_id');

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
            $content,
            $parentId ? (int) $parentId : null
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
                'comment',
                $this->currentUser['id'],
                (int) $projectId
            );
        }

        // Parse @mentions and create notifications
        $this->parseMentions($content, (int) $projectId);

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
                'follow',
                $this->currentUser['id'],
                null
            );

            // Check for follower badges for the followed user
            $badgeChecker = new BadgeChecker();
            $badgeChecker->checkFollowerBadges($userId);
        }

        return $this->respond([
            'success'   => true,
            'action'    => $result['action'],
            'following' => $result['following'],
            'message'   => $result['following'] ? 'Takip ediyorsunuz!' : 'Takibi bıraktınız.',
        ]);
    }

    /**
     * Search users (for @mention autocomplete)
     */
    public function searchUsers()
    {
        $query = trim($this->request->getGet('q') ?? '');

        if (strlen($query) < 2) {
            return $this->respond([
                'success' => true,
                'users'   => [],
            ]);
        }

        $userModel = model('UserModel');
        $excludeId = $this->isLoggedIn() ? $this->currentUser['id'] : null;
        $users = $userModel->search($query, 10, $excludeId);

        return $this->respond([
            'success' => true,
            'users'   => $users,
        ]);
    }

    /**
     * Parse @mentions in content and create notifications
     */
    private function parseMentions(string $content, int $projectId): void
    {
        // Find all @mentions
        preg_match_all('/@([^\s@]+)/', $content, $matches);

        if (empty($matches[1])) {
            return;
        }

        $userModel = model('UserModel');
        $mentionedUserIds = [];

        foreach ($matches[1] as $username) {
            // Clean up the username (remove trailing punctuation)
            $username = rtrim($username, '.,!?;:');

            if (empty($username)) {
                continue;
            }

            // Find user by name
            $user = $userModel->findByName($username);

            if ($user && $user['id'] != $this->currentUser['id'] && !in_array($user['id'], $mentionedUserIds)) {
                // Create mention notification
                $this->notificationModel->createNotification(
                    $user['id'],
                    'mention',
                    $this->currentUser['id'],
                    $projectId,
                    'Bir yorumda sizi etiketledi'
                );

                $mentionedUserIds[] = $user['id'];
            }
        }
    }

    // ================================
    // MESSAGING API ENDPOINTS
    // ================================

    /**
     * Send a message
     */
    public function sendMessage()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Mesaj göndermek için giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        if ($this->isBanned()) {
            return $this->respond([
                'success' => false,
                'message' => 'Hesabınız askıya alınmıştır.',
            ], 403);
        }

        $recipientId = (int) $this->request->getPost('recipient_id');
        $conversationId = (int) $this->request->getPost('conversation_id');
        $content = trim($this->request->getPost('content') ?? '');

        // Validate content
        if (strlen($content) < 1) {
            return $this->respond([
                'success' => false,
                'message' => 'Mesaj boş olamaz.',
            ], 400);
        }

        if (strlen($content) > 2000) {
            return $this->respond([
                'success' => false,
                'message' => 'Mesaj en fazla 2000 karakter olabilir.',
            ], 400);
        }

        // Get or create conversation
        if ($conversationId > 0) {
            // Verify user is participant
            if (!$this->conversationModel->isParticipant($conversationId, $this->currentUser['id'])) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Bu konuşmaya erişim izniniz yok.',
                ], 403);
            }
            $recipientId = $this->conversationModel->getOtherParticipant($conversationId, $this->currentUser['id']);
        } elseif ($recipientId > 0) {
            // Can't message yourself
            if ($recipientId === $this->currentUser['id']) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Kendinize mesaj gönderemezsiniz.',
                ], 400);
            }

            // Check if recipient exists
            $userModel = model('UserModel');
            $recipient = $userModel->find($recipientId);
            if (!$recipient) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı.',
                ], 404);
            }

            // Check if recipient follows sender (spam prevention)
            if (!$this->followModel->isFollowing($recipientId, $this->currentUser['id'])) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Bu kullanıcıya mesaj göndermek için takip edilmeniz gerekiyor.',
                    'needsFollow' => true,
                ], 403);
            }

            $conversation = $this->conversationModel->getOrCreateConversation($this->currentUser['id'], $recipientId);
            $conversationId = $conversation['id'];
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Alıcı veya konuşma ID gerekli.',
            ], 400);
        }

        // Sanitize content
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        // Send message
        $message = $this->messageModel->sendMessage($conversationId, $this->currentUser['id'], $content);

        if (!$message) {
            return $this->respond([
                'success' => false,
                'message' => 'Mesaj gönderilemedi.',
            ], 500);
        }

        // Create notification for recipient
        $this->notificationModel->createNotification(
            $recipientId,
            'message',
            $this->currentUser['id'],
            null,
            mb_substr(strip_tags($content), 0, 50) . (mb_strlen($content) > 50 ? '...' : '')
        );

        return $this->respond([
            'success' => true,
            'message' => 'Mesaj gönderildi!',
            'data'    => $message,
            'conversation_id' => $conversationId,
        ]);
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(int $conversationId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        // Verify user is participant
        if (!$this->conversationModel->isParticipant($conversationId, $this->currentUser['id'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Bu konuşmaya erişim izniniz yok.',
            ], 403);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $messages = $this->messageModel->getByConversation($conversationId, $limit, $offset);

        // Mark messages as read
        $this->messageModel->markAsRead($conversationId, $this->currentUser['id']);

        return $this->respond([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Poll for new messages in a conversation
     */
    public function pollMessages(int $conversationId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        // Verify user is participant
        if (!$this->conversationModel->isParticipant($conversationId, $this->currentUser['id'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Bu konuşmaya erişim izniniz yok.',
            ], 403);
        }

        $lastId = (int) ($this->request->getGet('last_id') ?? 0);

        $messages = $this->messageModel->getMessagesAfter($conversationId, $lastId);

        // Mark messages as read
        if (!empty($messages)) {
            $this->messageModel->markAsRead($conversationId, $this->currentUser['id']);
        }

        return $this->respond([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Mark messages in a conversation as read
     */
    public function markMessagesRead(int $conversationId)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        // Verify user is participant
        if (!$this->conversationModel->isParticipant($conversationId, $this->currentUser['id'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Bu konuşmaya erişim izniniz yok.',
            ], 403);
        }

        $this->messageModel->markAsRead($conversationId, $this->currentUser['id']);

        return $this->respond([
            'success' => true,
            'message' => 'Mesajlar okundu olarak işaretlendi.',
        ]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadMessageCount()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'count'   => 0,
            ]);
        }

        $count = $this->messageModel->getUnreadCount($this->currentUser['id']);

        return $this->respond([
            'success' => true,
            'count'   => $count,
        ]);
    }

    /**
     * Get conversation list for current user
     */
    public function getConversations()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Giriş yapmalısınız.',
                'requireAuth' => true,
            ], 401);
        }

        $conversations = $this->conversationModel->getConversationsForUser($this->currentUser['id']);

        return $this->respond([
            'success'       => true,
            'conversations' => $conversations,
        ]);
    }
}
