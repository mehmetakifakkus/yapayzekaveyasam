<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'conversation_id',
        'sender_id',
        'content',
        'is_read',
        'read_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get messages for a conversation with pagination
     */
    public function getByConversation(int $conversationId, int $limit = 50, int $offset = 0): array
    {
        return $this->select('messages.*, users.name as sender_name, users.avatar as sender_avatar')
            ->join('users', 'users.id = messages.sender_id')
            ->where('conversation_id', $conversationId)
            ->orderBy('messages.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get messages after a specific message ID (for polling)
     */
    public function getMessagesAfter(int $conversationId, int $afterId): array
    {
        return $this->select('messages.*, users.name as sender_name, users.avatar as sender_avatar')
            ->join('users', 'users.id = messages.sender_id')
            ->where('conversation_id', $conversationId)
            ->where('messages.id >', $afterId)
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Send a new message
     */
    public function sendMessage(int $conversationId, int $senderId, string $content): ?array
    {
        $this->insert([
            'conversation_id' => $conversationId,
            'sender_id'       => $senderId,
            'content'         => $content,
        ]);

        $messageId = $this->getInsertID();

        if (!$messageId) {
            return null;
        }

        // Update conversation's last_message_at
        $conversationModel = model('ConversationModel');
        $conversationModel->updateLastMessageTime($conversationId);

        // Return the message with sender info
        return $this->select('messages.*, users.name as sender_name, users.avatar as sender_avatar')
            ->join('users', 'users.id = messages.sender_id')
            ->find($messageId);
    }

    /**
     * Mark all messages in a conversation as read for a user
     */
    public function markAsRead(int $conversationId, int $userId): int
    {
        return $this->where('conversation_id', $conversationId)
            ->where('sender_id !=', $userId)
            ->where('is_read', 0)
            ->set([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

    /**
     * Get total unread message count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        $conversationModel = model('ConversationModel');

        // Get all conversation IDs where user is a participant
        $conversations = $conversationModel
            ->select('id')
            ->groupStart()
                ->where('user_one_id', $userId)
                ->orWhere('user_two_id', $userId)
            ->groupEnd()
            ->findAll();

        if (empty($conversations)) {
            return 0;
        }

        $conversationIds = array_column($conversations, 'id');

        return $this->whereIn('conversation_id', $conversationIds)
            ->where('sender_id !=', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Get last message in a conversation
     */
    public function getLastMessage(int $conversationId): ?array
    {
        return $this->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Count messages in a conversation
     */
    public function getCountByConversation(int $conversationId): int
    {
        return $this->where('conversation_id', $conversationId)->countAllResults();
    }
}
