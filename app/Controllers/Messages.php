<?php

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\MessageModel;
use App\Models\FollowModel;
use App\Models\UserModel;

class Messages extends BaseController
{
    protected ConversationModel $conversationModel;
    protected MessageModel $messageModel;
    protected FollowModel $followModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->conversationModel = model('ConversationModel');
        $this->messageModel = model('MessageModel');
        $this->followModel = model('FollowModel');
        $this->userModel = model('UserModel');
    }

    /**
     * Messages inbox page
     */
    public function index()
    {
        $this->requireAuth();

        $conversations = $this->conversationModel->getConversationsForUser($this->currentUser['id']);

        // If there are conversations, show the first one by default
        $activeConversation = null;
        $messages = [];
        $otherUser = null;

        if (!empty($conversations)) {
            $activeConversation = $conversations[0];
            $otherUser = $activeConversation['other_user'];
            $messages = $this->messageModel->getByConversation($activeConversation['id'], 50, 0);

            // Mark messages as read
            $this->messageModel->markAsRead($activeConversation['id'], $this->currentUser['id']);

            // Update unread count for the active conversation
            $activeConversation['unread_count'] = 0;
            $conversations[0]['unread_count'] = 0;
        }

        return view('pages/messages', $this->getViewData([
            'title'              => 'Mesajlar',
            'conversations'      => $conversations,
            'activeConversation' => $activeConversation,
            'otherUser'          => $otherUser,
            'messages'           => array_reverse($messages), // Oldest first
        ]));
    }

    /**
     * View a specific conversation
     */
    public function conversation(int $conversationId)
    {
        $this->requireAuth();

        // Verify user is participant
        if (!$this->conversationModel->isParticipant($conversationId, $this->currentUser['id'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $conversations = $this->conversationModel->getConversationsForUser($this->currentUser['id']);

        // Find the active conversation
        $activeConversation = null;
        foreach ($conversations as &$conv) {
            if ($conv['id'] == $conversationId) {
                $activeConversation = $conv;
                break;
            }
        }

        if (!$activeConversation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $otherUser = $activeConversation['other_user'];
        $messages = $this->messageModel->getByConversation($conversationId, 50, 0);

        // Mark messages as read
        $this->messageModel->markAsRead($conversationId, $this->currentUser['id']);

        // Update unread count for UI
        foreach ($conversations as &$conv) {
            if ($conv['id'] == $conversationId) {
                $conv['unread_count'] = 0;
                break;
            }
        }

        return view('pages/messages', $this->getViewData([
            'title'              => 'Mesajlar - ' . esc($otherUser['name']),
            'conversations'      => $conversations,
            'activeConversation' => $activeConversation,
            'otherUser'          => $otherUser,
            'messages'           => array_reverse($messages), // Oldest first
        ]));
    }

    /**
     * Start a new conversation with a user
     */
    public function newConversation(int $userId)
    {
        $this->requireAuth();

        // Can't message yourself
        if ($userId === $this->currentUser['id']) {
            return redirect()->to('/messages')->with('error', 'Kendinize mesaj gönderemezsiniz.');
        }

        // Check if user exists
        $targetUser = $this->userModel->find($userId);
        if (!$targetUser) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if target user follows current user (spam prevention)
        $canMessage = $this->followModel->isFollowing($userId, $this->currentUser['id']);

        // Check if there's an existing conversation
        $existingConversation = $this->conversationModel->findByUsers($this->currentUser['id'], $userId);

        if ($existingConversation) {
            // Redirect to existing conversation
            return redirect()->to('/messages/' . $existingConversation['id']);
        }

        // Get all conversations for sidebar
        $conversations = $this->conversationModel->getConversationsForUser($this->currentUser['id']);

        return view('pages/messages', $this->getViewData([
            'title'              => 'Yeni Mesaj - ' . esc($targetUser['name']),
            'conversations'      => $conversations,
            'activeConversation' => null,
            'otherUser'          => $targetUser,
            'messages'           => [],
            'isNewConversation'  => true,
            'canMessage'         => $canMessage,
            'recipientId'        => $userId,
        ]));
    }
}
