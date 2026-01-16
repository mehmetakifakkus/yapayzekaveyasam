<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversationModel extends Model
{
    protected $table            = 'conversations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get or create a conversation between two users
     * user_one_id is always the smaller ID, user_two_id is the larger
     */
    public function getOrCreateConversation(int $userId1, int $userId2): array
    {
        // Ensure consistent ordering
        $userOneId = min($userId1, $userId2);
        $userTwoId = max($userId1, $userId2);

        // Try to find existing conversation
        $conversation = $this->where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId)
            ->first();

        if ($conversation) {
            return $conversation;
        }

        // Create new conversation
        $this->insert([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
        ]);

        return $this->find($this->getInsertID());
    }

    /**
     * Get all conversations for a user with other user info and last message
     */
    public function getConversationsForUser(int $userId): array
    {
        $db = \Config\Database::connect();

        // Get all conversations where user is a participant
        $conversations = $this->select('conversations.*')
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderBy('last_message_at', 'DESC')
            ->findAll();

        if (empty($conversations)) {
            return [];
        }

        $userModel = model('UserModel');
        $messageModel = model('MessageModel');

        foreach ($conversations as &$conversation) {
            // Get the other user's info
            $otherUserId = $conversation['user_one_id'] == $userId
                ? $conversation['user_two_id']
                : $conversation['user_one_id'];

            $conversation['other_user'] = $userModel->select('id, name, avatar')->find($otherUserId);

            // Get last message
            $conversation['last_message'] = $messageModel
                ->where('conversation_id', $conversation['id'])
                ->orderBy('created_at', 'DESC')
                ->first();

            // Get unread count for this conversation
            $conversation['unread_count'] = $messageModel
                ->where('conversation_id', $conversation['id'])
                ->where('sender_id !=', $userId)
                ->where('is_read', 0)
                ->countAllResults();
        }

        return $conversations;
    }

    /**
     * Get conversation by ID with participants info
     */
    public function getConversationWithUsers(int $conversationId): ?array
    {
        $conversation = $this->find($conversationId);

        if (!$conversation) {
            return null;
        }

        $userModel = model('UserModel');
        $conversation['user_one'] = $userModel->select('id, name, avatar')->find($conversation['user_one_id']);
        $conversation['user_two'] = $userModel->select('id, name, avatar')->find($conversation['user_two_id']);

        return $conversation;
    }

    /**
     * Check if user is a participant in the conversation
     */
    public function isParticipant(int $conversationId, int $userId): bool
    {
        $conversation = $this->find($conversationId);

        if (!$conversation) {
            return false;
        }

        return $conversation['user_one_id'] == $userId || $conversation['user_two_id'] == $userId;
    }

    /**
     * Get the other participant ID in a conversation
     */
    public function getOtherParticipant(int $conversationId, int $userId): ?int
    {
        $conversation = $this->find($conversationId);

        if (!$conversation) {
            return null;
        }

        return $conversation['user_one_id'] == $userId
            ? (int) $conversation['user_two_id']
            : (int) $conversation['user_one_id'];
    }

    /**
     * Update last message timestamp
     */
    public function updateLastMessageTime(int $conversationId): void
    {
        $this->update($conversationId, [
            'last_message_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Count conversations with unread messages for a user
     */
    public function getUnreadConversationCount(int $userId): int
    {
        $db = \Config\Database::connect();

        // Get conversations where user is participant
        $conversationIds = $this->select('id')
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->findAll();

        if (empty($conversationIds)) {
            return 0;
        }

        $ids = array_column($conversationIds, 'id');

        // Count conversations with unread messages (not sent by user)
        return $db->table('messages')
            ->where('sender_id !=', $userId)
            ->where('is_read', 0)
            ->whereIn('conversation_id', $ids)
            ->distinct()
            ->select('conversation_id')
            ->countAllResults();
    }

    /**
     * Find existing conversation between two users
     */
    public function findByUsers(int $userId1, int $userId2): ?array
    {
        $userOneId = min($userId1, $userId2);
        $userTwoId = max($userId1, $userId2);

        return $this->where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId)
            ->first();
    }
}
