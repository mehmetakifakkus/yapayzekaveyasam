<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\AiToolModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\NotificationModel;

class Admin extends BaseController
{
    protected ProjectModel $projectModel;
    protected UserModel $userModel;
    protected CategoryModel $categoryModel;
    protected AiToolModel $aiToolModel;

    public function __construct()
    {
        $this->projectModel = model('ProjectModel');
        $this->userModel = model('UserModel');
        $this->categoryModel = model('CategoryModel');
        $this->aiToolModel = model('AiToolModel');
    }

    /**
     * Dashboard with statistics
     */
    public function index()
    {
        $this->requireAdmin();

        $db = \Config\Database::connect();

        // Statistics
        $stats = [
            'total_users' => $this->userModel->countAll(),
            'total_projects' => $this->projectModel->countAll(),
            'pending_projects' => $this->projectModel->where('status', 'pending')->countAllResults(),
            'approved_projects' => $this->projectModel->where('status', 'approved')->countAllResults(),
            'rejected_projects' => $this->projectModel->where('status', 'rejected')->countAllResults(),
            'total_likes' => $db->table('likes')->countAll(),
            'total_comments' => $db->table('comments')->countAll(),
            'banned_users' => $this->userModel->where('is_banned', 1)->countAllResults(),
        ];

        // Recent pending projects
        $pendingProjects = $this->projectModel
            ->select('projects.*, users.name as user_name, users.email as user_email, categories.name as category_name')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('projects.status', 'pending')
            ->orderBy('projects.created_at', 'DESC')
            ->limit(5)
            ->find();

        // Recent users
        $recentUsers = $this->userModel
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        return view('admin/dashboard', $this->getViewData([
            'title' => 'Admin Dashboard - AI Showcase',
            'stats' => $stats,
            'pendingProjects' => $pendingProjects,
            'recentUsers' => $recentUsers,
        ]));
    }

    /**
     * List all projects
     */
    public function projects()
    {
        $this->requireAdmin();

        $status = $this->request->getGet('status') ?? 'all';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $builder = $this->projectModel
            ->select('projects.*, users.name as user_name, users.email as user_email, categories.name as category_name')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id');

        if ($status !== 'all') {
            $builder->where('projects.status', $status);
        }

        $totalProjects = $builder->countAllResults(false);
        $projects = $builder
            ->orderBy('projects.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->find();

        // Get AI tools for each project
        foreach ($projects as &$project) {
            $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);
        }

        $totalPages = ceil($totalProjects / $perPage);

        return view('admin/projects', $this->getViewData([
            'title' => 'Proje Yönetimi - Admin',
            'projects' => $projects,
            'currentStatus' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProjects' => $totalProjects,
        ]));
    }

    /**
     * Approve a project
     */
    public function approveProject($id)
    {
        $this->requireAdmin();

        $project = $this->projectModel->find($id);

        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Proje bulunamadı.']);
        }

        $this->projectModel->update($id, [
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        // Create notification for the project owner
        $notificationModel = model('NotificationModel');
        $notificationModel->createNotification(
            $project['user_id'],
            null, // No actor for system notification
            'approve',
            $id
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Proje onaylandı.']);
        }

        return redirect()->back()->with('success', 'Proje onaylandı.');
    }

    /**
     * Reject a project
     */
    public function rejectProject($id)
    {
        $this->requireAdmin();

        $project = $this->projectModel->find($id);

        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Proje bulunamadı.']);
        }

        $reason = $this->request->getPost('reason') ?? '';

        $this->projectModel->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Proje reddedildi.']);
        }

        return redirect()->back()->with('success', 'Proje reddedildi.');
    }

    /**
     * Delete a project
     */
    public function deleteProject($id)
    {
        $this->requireAdmin();

        $project = $this->projectModel->find($id);

        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Proje bulunamadı.']);
        }

        // Delete screenshot
        if ($project['screenshot'] && file_exists(FCPATH . $project['screenshot'])) {
            unlink(FCPATH . $project['screenshot']);
        }

        // Delete related records
        $db = \Config\Database::connect();
        $db->table('project_ai_tools')->where('project_id', $id)->delete();
        $db->table('likes')->where('project_id', $id)->delete();
        $db->table('comments')->where('project_id', $id)->delete();

        $this->projectModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Proje silindi.']);
        }

        return redirect()->back()->with('success', 'Proje silindi.');
    }

    /**
     * List all users
     */
    public function users()
    {
        $this->requireAdmin();

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $db = \Config\Database::connect();

        $totalUsers = $this->userModel->countAll();
        $users = $this->userModel
            ->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->find();

        // Get project counts for each user
        foreach ($users as &$user) {
            $user['projects_count'] = $this->projectModel
                ->where('user_id', $user['id'])
                ->countAllResults();

            $user['approved_projects'] = $this->projectModel
                ->where('user_id', $user['id'])
                ->where('status', 'approved')
                ->countAllResults();
        }

        $totalPages = ceil($totalUsers / $perPage);

        return view('admin/users', $this->getViewData([
            'title' => 'Kullanıcı Yönetimi - Admin',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
        ]));
    }

    /**
     * Ban a user
     */
    public function banUser($id)
    {
        $this->requireAdmin();

        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kullanıcı bulunamadı.']);
        }

        // Don't allow banning admins
        $adminEmails = getenv('admin.emails') ?: '';
        $adminEmailList = array_map('trim', explode(',', $adminEmails));
        if (in_array($user['email'], $adminEmailList)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Admin kullanıcılar yasaklanamaz.']);
        }

        $this->userModel->update($id, ['is_banned' => 1]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Kullanıcı yasaklandı.']);
        }

        return redirect()->back()->with('success', 'Kullanıcı yasaklandı.');
    }

    /**
     * Unban a user
     */
    public function unbanUser($id)
    {
        $this->requireAdmin();

        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kullanıcı bulunamadı.']);
        }

        $this->userModel->update($id, ['is_banned' => 0]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Kullanıcı yasağı kaldırıldı.']);
        }

        return redirect()->back()->with('success', 'Kullanıcı yasağı kaldırıldı.');
    }
}
