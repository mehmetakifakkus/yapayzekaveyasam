<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\AiToolModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\NotificationModel;
use App\Libraries\ScreenshotService;

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

    /**
     * Settings page
     */
    public function settings()
    {
        $this->requireAdmin();

        $currentTheme = getenv('APP_THEME') ?: 'default';

        return view('admin/settings', $this->getViewData([
            'title' => 'Ayarlar - Admin',
            'currentTheme' => $currentTheme,
        ]));
    }

    /**
     * Update settings
     */
    public function updateSettings()
    {
        $this->requireAdmin();

        $theme = $this->request->getPost('theme');
        $validThemes = ['default', 'emerald', 'amber', 'ocean', 'mono', 'light-white', 'light-cream', 'light-gray'];

        if (!in_array($theme, $validThemes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Geçersiz tema seçimi.',
            ]);
        }

        // Update .env file
        $envPath = ROOTPATH . '.env';
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            // Check if APP_THEME exists
            if (preg_match('/^APP_THEME\s*=.*/m', $envContent)) {
                $envContent = preg_replace('/^APP_THEME\s*=.*/m', "APP_THEME = {$theme}", $envContent);
            } else {
                $envContent .= "\nAPP_THEME = {$theme}\n";
            }

            file_put_contents($envPath, $envContent);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Tema güncellendi.',
            'theme' => $theme,
        ]);
    }

    /**
     * Analytics page with charts
     */
    public function analytics()
    {
        $this->requireAdmin();

        $db = \Config\Database::connect();

        // Get data for the past 30 days
        $days = 30;
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        // Daily registrations
        $dailyUsers = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM users
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$startDate])->getResultArray();

        // Daily projects
        $dailyProjects = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM projects
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$startDate])->getResultArray();

        // Daily likes
        $dailyLikes = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM likes
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$startDate])->getResultArray();

        // Daily comments
        $dailyComments = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM comments
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$startDate])->getResultArray();

        // Projects by category
        $projectsByCategory = $db->query("
            SELECT c.name, COUNT(p.id) as count
            FROM categories c
            LEFT JOIN projects p ON p.category_id = c.id AND p.status = 'approved'
            GROUP BY c.id, c.name
            ORDER BY count DESC
        ")->getResultArray();

        // Projects by AI tool
        $projectsByTool = $db->query("
            SELECT t.name, COUNT(pt.project_id) as count
            FROM ai_tools t
            LEFT JOIN project_ai_tools pt ON pt.ai_tool_id = t.id
            LEFT JOIN projects p ON p.id = pt.project_id AND p.status = 'approved'
            GROUP BY t.id, t.name
            ORDER BY count DESC
        ")->getResultArray();

        // Top users by projects
        $topUsersByProjects = $db->query("
            SELECT u.id, u.name, u.avatar, COUNT(p.id) as projects_count
            FROM users u
            INNER JOIN projects p ON p.user_id = u.id AND p.status = 'approved'
            GROUP BY u.id, u.name, u.avatar
            ORDER BY projects_count DESC
            LIMIT 10
        ")->getResultArray();

        // Top users by likes received
        $topUsersByLikes = $db->query("
            SELECT u.id, u.name, u.avatar, COUNT(l.id) as likes_count
            FROM users u
            INNER JOIN projects p ON p.user_id = u.id AND p.status = 'approved'
            INNER JOIN likes l ON l.project_id = p.id
            GROUP BY u.id, u.name, u.avatar
            ORDER BY likes_count DESC
            LIMIT 10
        ")->getResultArray();

        // Top projects by likes
        $topProjects = $db->query("
            SELECT p.id, p.title, p.slug, p.screenshot, u.name as user_name, COUNT(l.id) as likes_count
            FROM projects p
            INNER JOIN users u ON u.id = p.user_id
            LEFT JOIN likes l ON l.project_id = p.id
            WHERE p.status = 'approved'
            GROUP BY p.id, p.title, p.slug, p.screenshot, u.name
            ORDER BY likes_count DESC
            LIMIT 10
        ")->getResultArray();

        // Project status distribution
        $projectStatuses = [
            'approved' => $this->projectModel->where('status', 'approved')->countAllResults(),
            'pending' => $this->projectModel->where('status', 'pending')->countAllResults(),
            'rejected' => $this->projectModel->where('status', 'rejected')->countAllResults(),
        ];

        return view('admin/analytics', $this->getViewData([
            'title' => 'Analytics - Admin',
            'dailyUsers' => $dailyUsers,
            'dailyProjects' => $dailyProjects,
            'dailyLikes' => $dailyLikes,
            'dailyComments' => $dailyComments,
            'projectsByCategory' => $projectsByCategory,
            'projectsByTool' => $projectsByTool,
            'topUsersByProjects' => $topUsersByProjects,
            'topUsersByLikes' => $topUsersByLikes,
            'topProjects' => $topProjects,
            'projectStatuses' => $projectStatuses,
            'days' => $days,
        ]));
    }

    /**
     * Refresh project screenshot
     */
    public function refreshScreenshot(int $id)
    {
        $this->requireAdmin();

        $project = $this->projectModel->find($id);

        if (!$project) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Proje bulunamadı.',
            ]);
        }

        if (empty($project['website_url'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Proje için website URL\'si tanımlı değil.',
            ]);
        }

        $screenshotService = new ScreenshotService();

        if (!$screenshotService->isConfigured()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Screenshot servisi yapılandırılmamış. .env dosyasında THUMBNAIL_WS_API_KEY ayarlayın.',
            ]);
        }

        // Delete old screenshot if exists
        if ($project['screenshot'] && file_exists(FCPATH . $project['screenshot'])) {
            unlink(FCPATH . $project['screenshot']);
        }

        // Capture new screenshot
        $screenshotPath = $screenshotService->capture($project['website_url']);

        if (!$screenshotPath) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Screenshot alınamadı. Lütfen URL\'nin erişilebilir olduğundan emin olun.',
            ]);
        }

        // Update project
        $this->projectModel->update($id, ['screenshot' => $screenshotPath]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Screenshot başarıyla güncellendi.',
            'screenshot' => base_url($screenshotPath),
        ]);
    }
}
