<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;

class Notifications extends BaseController
{
    use ResponseTrait;

    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = model('NotificationModel');
    }

    /**
     * List notifications page
     */
    public function index()
    {
        $this->requireAuth();

        $notifications = $this->notificationModel->getForUser($this->currentUser['id'], 50);

        return view('pages/notifications', $this->getViewData([
            'title' => 'Bildirimler - AI Showcase',
            'notifications' => $notifications,
        ]));
    }

    /**
     * Get notifications for dropdown (API)
     */
    public function getRecent()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond(['success' => false, 'message' => 'Giriş yapmalısınız.'], 401);
        }

        $notifications = $this->notificationModel->getForUser($this->currentUser['id'], 10);
        $unreadCount = $this->notificationModel->getUnreadCount($this->currentUser['id']);

        return $this->respond([
            'success' => true,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markRead(int $id)
    {
        if (!$this->isLoggedIn()) {
            return $this->respond(['success' => false, 'message' => 'Giriş yapmalısınız.'], 401);
        }

        $this->notificationModel->markAsRead($id, $this->currentUser['id']);

        return $this->respond(['success' => true]);
    }

    /**
     * Mark all as read
     */
    public function markAllRead()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond(['success' => false, 'message' => 'Giriş yapmalısınız.'], 401);
        }

        $this->notificationModel->markAllAsRead($this->currentUser['id']);

        if ($this->request->isAJAX()) {
            return $this->respond(['success' => true]);
        }

        return redirect()->to('/notifications');
    }

    /**
     * Get unread count (API)
     */
    public function unreadCount()
    {
        if (!$this->isLoggedIn()) {
            return $this->respond(['count' => 0]);
        }

        $count = $this->notificationModel->getUnreadCount($this->currentUser['id']);

        return $this->respond(['count' => $count]);
    }
}
