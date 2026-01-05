<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\LikeModel;
use App\Models\AiToolModel;

class Users extends BaseController
{
    protected UserModel $userModel;
    protected ProjectModel $projectModel;
    protected LikeModel $likeModel;
    protected AiToolModel $aiToolModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->projectModel = model('ProjectModel');
        $this->likeModel = model('LikeModel');
        $this->aiToolModel = model('AiToolModel');
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

        // Check if this is the current user's profile
        $isOwnProfile = $this->isLoggedIn() && $this->currentUser['id'] === $id;

        return view('pages/profile', $this->getViewData([
            'title'         => $user['name'] . ' - AI Showcase',
            'user'          => $user,
            'projects'      => $projects,
            'projectsCount' => $projectsCount,
            'likesReceived' => $likesReceived,
            'isOwnProfile'  => $isOwnProfile,
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
}
