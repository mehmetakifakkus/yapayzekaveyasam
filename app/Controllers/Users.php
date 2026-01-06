<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\LikeModel;
use App\Models\AiToolModel;
use App\Models\FollowModel;
use App\Models\BookmarkModel;

class Users extends BaseController
{
    protected UserModel $userModel;
    protected ProjectModel $projectModel;
    protected LikeModel $likeModel;
    protected AiToolModel $aiToolModel;
    protected FollowModel $followModel;
    protected BookmarkModel $bookmarkModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->projectModel = model('ProjectModel');
        $this->likeModel = model('LikeModel');
        $this->aiToolModel = model('AiToolModel');
        $this->followModel = model('FollowModel');
        $this->bookmarkModel = model('BookmarkModel');
    }

    /**
     * Show user profile
     */
    public function profile(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kullanıcı bulunamadı.');
        }

        // Get user's projects
        $projects = $this->projectModel->getByUserId($id);

        // Enrich projects
        foreach ($projects as &$project) {
            $project['likes_count'] = $this->likeModel->getCountByProject($project['id']);
            $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);

            if ($this->isLoggedIn()) {
                $project['is_liked'] = $this->likeModel->hasLiked($this->currentUser['id'], $project['id']);
            } else {
                $project['is_liked'] = false;
            }
        }

        // Get stats
        $projectsCount = $this->userModel->getProjectsCount($id);
        $likesReceived = $this->userModel->getTotalLikesReceived($id);

        // Get follow stats
        $followerCount = $this->followModel->getFollowerCount($id);
        $followingCount = $this->followModel->getFollowingCount($id);

        // Check if current user is following this user
        $isFollowing = false;
        if ($this->isLoggedIn() && $this->currentUser['id'] !== $id) {
            $isFollowing = $this->followModel->isFollowing($this->currentUser['id'], $id);
        }

        // Check if this is the current user's profile
        $isOwnProfile = $this->isLoggedIn() && $this->currentUser['id'] === $id;

        return view('pages/profile', $this->getViewData([
            'title'          => $user['name'] . ' - AI Showcase',
            'user'           => $user,
            'projects'       => $projects,
            'projectsCount'  => $projectsCount,
            'likesReceived'  => $likesReceived,
            'followerCount'  => $followerCount,
            'followingCount' => $followingCount,
            'isFollowing'    => $isFollowing,
            'isOwnProfile'   => $isOwnProfile,
        ]));
    }

    /**
     * Update user bio
     */
    public function updateBio()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Giriş yapmalısınız.',
            ]);
        }

        $bio = trim($this->request->getPost('bio') ?? '');

        if (strlen($bio) > 500) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bio en fazla 500 karakter olabilir.',
            ]);
        }

        $bio = htmlspecialchars($bio, ENT_QUOTES, 'UTF-8');

        $this->userModel->update($this->currentUser['id'], ['bio' => $bio]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Bio güncellendi!',
            'bio'     => $bio,
        ]);
    }

    /**
     * Update user theme
     */
    public function updateTheme()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Giriş yapmalısınız.',
            ]);
        }

        $theme = $this->request->getPost('theme') ?? 'default';

        $validThemes = ['default', 'emerald', 'amber', 'ocean', 'mono', 'light-white', 'light-cream', 'light-gray'];

        if (!in_array($theme, $validThemes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Geçersiz tema.',
            ]);
        }

        $this->userModel->update($this->currentUser['id'], ['theme' => $theme]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Tema güncellendi!',
            'theme'   => $theme,
        ]);
    }

    /**
     * Show user's bookmarked projects
     */
    public function bookmarks(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kullanıcı bulunamadı.');
        }

        // Only show bookmarks if it's the current user's profile
        $isOwnProfile = $this->isLoggedIn() && $this->currentUser['id'] === $id;

        if (!$isOwnProfile) {
            return redirect()->to('/user/' . $id);
        }

        // Get bookmarked projects
        $projects = $this->bookmarkModel->getBookmarkedProjects($id);

        // Enrich projects with AI tools
        foreach ($projects as &$project) {
            $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);
            $project['is_liked'] = $this->likeModel->hasLiked($this->currentUser['id'], $project['id']);
            $project['is_bookmarked'] = true;
        }

        return view('pages/bookmarks', $this->getViewData([
            'title'    => 'Kaydedilenler - AI Showcase',
            'user'     => $user,
            'projects' => $projects,
        ]));
    }

    /**
     * Show feed from followed users
     */
    public function feed()
    {
        $this->requireAuth();

        $userId = $this->currentUser['id'];
        $followingIds = $this->followModel->getFollowingIds($userId);

        if (empty($followingIds)) {
            $projects = [];
        } else {
            $projects = $this->projectModel
                ->select('projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name, categories.slug as category_slug')
                ->select('(SELECT COUNT(*) FROM likes WHERE likes.project_id = projects.id) as likes_count', false)
                ->join('users', 'users.id = projects.user_id')
                ->join('categories', 'categories.id = projects.category_id')
                ->whereIn('projects.user_id', $followingIds)
                ->where('projects.status', 'approved')
                ->orderBy('projects.created_at', 'DESC')
                ->limit(50)
                ->findAll();

            // Enrich projects
            foreach ($projects as &$project) {
                $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);
                $project['is_liked'] = $this->likeModel->hasLiked($userId, $project['id']);
                $project['is_bookmarked'] = $this->bookmarkModel->hasBookmarked($userId, $project['id']);
            }
        }

        // Get following users for sidebar
        $followingUsers = $this->followModel->getFollowing($userId, 10);

        return view('pages/feed', $this->getViewData([
            'title'          => 'Takip Akışı - AI Showcase',
            'projects'       => $projects,
            'followingUsers' => $followingUsers,
        ]));
    }
}
