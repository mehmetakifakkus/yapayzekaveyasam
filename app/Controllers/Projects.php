<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CategoryModel;
use App\Models\AiToolModel;
use App\Models\LikeModel;
use App\Models\CommentModel;

class Projects extends BaseController
{
    protected ProjectModel $projectModel;
    protected CategoryModel $categoryModel;
    protected AiToolModel $aiToolModel;
    protected LikeModel $likeModel;
    protected CommentModel $commentModel;

    public function __construct()
    {
        $this->projectModel = model('ProjectModel');
        $this->categoryModel = model('CategoryModel');
        $this->aiToolModel = model('AiToolModel');
        $this->likeModel = model('LikeModel');
        $this->commentModel = model('CommentModel');
    }

    /**
     * List all projects with filters
     */
    public function index()
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
        $this->enrichProjects($projects);

        $categories = $this->categoryModel->getAllWithProjectCount();
        $aiTools = $this->aiToolModel->getAllWithProjectCount();

        return view('pages/projects_list', $this->getViewData([
            'title'         => 'Projeler - AI Showcase',
            'projects'      => $projects,
            'categories'    => $categories,
            'aiTools'       => $aiTools,
            'filters'       => $filters,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalProjects' => $totalProjects,
        ]));
    }

    /**
     * Show single project
     */
    public function show(string $slug)
    {
        $project = $this->projectModel->findBySlug($slug);

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Proje bulunamadı.');
        }

        // Increment views
        $this->projectModel->incrementViews($project['id']);

        // Get AI tools
        $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);

        // Get likes
        $project['likes_count'] = $this->likeModel->getCountByProject($project['id']);
        $project['is_liked'] = $this->isLoggedIn()
            ? $this->likeModel->hasLiked($this->currentUser['id'], $project['id'])
            : false;

        // Get comments
        $comments = $this->commentModel->getByProjectId($project['id']);
        $project['comments_count'] = $this->commentModel->getCountByProject($project['id']);

        // Get related projects (same category)
        $relatedProjects = $this->projectModel->getApprovedProjects(4, 0, [
            'category' => $project['category_slug'],
        ]);
        $relatedProjects = array_filter($relatedProjects, fn($p) => $p['id'] !== $project['id']);
        $relatedProjects = array_slice($relatedProjects, 0, 3);
        $this->enrichProjects($relatedProjects);

        return view('pages/project_detail', $this->getViewData([
            'title'           => $project['title'] . ' - AI Showcase',
            'project'         => $project,
            'comments'        => $comments,
            'relatedProjects' => $relatedProjects,
        ]));
    }

    /**
     * Show create form
     */
    public function create()
    {
        if (!$this->isLoggedIn()) {
            $this->session->set('redirect_after_login', current_url());
            return redirect()->to('/auth/google');
        }

        $this->requireNotBanned();

        $categories = $this->categoryModel->findAll();
        $aiTools = $this->aiToolModel->findAll();

        return view('pages/project_form', $this->getViewData([
            'title'      => 'Yeni Proje Ekle - AI Showcase',
            'categories' => $categories,
            'aiTools'    => $aiTools,
            'project'    => null,
            'isEdit'     => false,
        ]));
    }

    /**
     * Store new project
     */
    public function store()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/auth/google');
        }

        $this->requireNotBanned();

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'website_url' => 'required|valid_url',
            'category_id' => 'required|integer',
            'ai_tools'    => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_id'     => $this->currentUser['id'],
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'slug'        => $this->projectModel->generateSlug($this->request->getPost('title')),
            'description' => $this->request->getPost('description'),
            'website_url' => $this->request->getPost('website_url'),
            'github_url'  => $this->request->getPost('github_url') ?: null,
            'status'      => $this->isAdmin() ? 'approved' : 'pending',
        ];

        // Handle screenshot upload
        $screenshot = $this->request->getFile('screenshot');
        if ($screenshot && $screenshot->isValid() && !$screenshot->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($screenshot->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()->with('error', 'Sadece resim dosyaları yüklenebilir.');
            }

            // Max 2MB file size
            if ($screenshot->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Dosya boyutu maksimum 2MB olabilir.');
            }

            $newName = $screenshot->getRandomName();
            $screenshot->move(FCPATH . 'uploads/screenshots', $newName);
            $data['screenshot'] = 'uploads/screenshots/' . $newName;
        }

        $projectId = $this->projectModel->insert($data);

        if (!$projectId) {
            return redirect()->back()->withInput()->with('error', 'Proje eklenirken bir hata oluştu.');
        }

        // Add AI tools
        $aiToolIds = $this->request->getPost('ai_tools');
        if (is_array($aiToolIds)) {
            $db = \Config\Database::connect();
            foreach ($aiToolIds as $toolId) {
                $db->table('project_ai_tools')->insert([
                    'project_id' => $projectId,
                    'ai_tool_id' => (int) $toolId,
                ]);
            }
        }

        $message = $this->isAdmin()
            ? 'Projeniz başarıyla eklendi!'
            : 'Projeniz başarıyla eklendi! Admin onayından sonra yayınlanacaktır.';

        return redirect()->to('/projects/' . $data['slug'])->with('success', $message);
    }

    /**
     * Show edit form
     */
    public function edit(string $slug)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/auth/google');
        }

        $this->requireNotBanned();

        $project = $this->projectModel->findBySlug($slug);

        if (!$project || $project['user_id'] !== $this->currentUser['id']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categories = $this->categoryModel->findAll();
        $aiTools = $this->aiToolModel->findAll();
        $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);
        $project['ai_tool_ids'] = array_column($project['ai_tools'], 'id');

        return view('pages/project_form', $this->getViewData([
            'title'      => 'Projeyi Düzenle - AI Showcase',
            'categories' => $categories,
            'aiTools'    => $aiTools,
            'project'    => $project,
            'isEdit'     => true,
        ]));
    }

    /**
     * Update project
     */
    public function update(string $slug)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/auth/google');
        }

        $this->requireNotBanned();

        $project = $this->projectModel->findBySlug($slug);

        if (!$project || $project['user_id'] !== $this->currentUser['id']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'website_url' => 'required|valid_url',
            'category_id' => 'required|integer',
            'ai_tools'    => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'website_url' => $this->request->getPost('website_url'),
            'github_url'  => $this->request->getPost('github_url') ?: null,
        ];

        // Update slug if title changed
        if ($data['title'] !== $project['title']) {
            $data['slug'] = $this->projectModel->generateSlug($data['title']);
        }

        // Handle screenshot upload
        $screenshot = $this->request->getFile('screenshot');
        if ($screenshot && $screenshot->isValid() && !$screenshot->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($screenshot->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()->with('error', 'Sadece resim dosyaları yüklenebilir.');
            }

            // Max 2MB file size
            if ($screenshot->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Dosya boyutu maksimum 2MB olabilir.');
            }

            // Delete old screenshot
            if ($project['screenshot'] && file_exists(FCPATH . $project['screenshot'])) {
                unlink(FCPATH . $project['screenshot']);
            }

            $newName = $screenshot->getRandomName();
            $screenshot->move(FCPATH . 'uploads/screenshots', $newName);
            $data['screenshot'] = 'uploads/screenshots/' . $newName;
        }

        $this->projectModel->update($project['id'], $data);

        // Update AI tools
        $db = \Config\Database::connect();
        $db->table('project_ai_tools')->where('project_id', $project['id'])->delete();

        $aiToolIds = $this->request->getPost('ai_tools');
        if (is_array($aiToolIds)) {
            foreach ($aiToolIds as $toolId) {
                $db->table('project_ai_tools')->insert([
                    'project_id' => $project['id'],
                    'ai_tool_id' => (int) $toolId,
                ]);
            }
        }

        $newSlug = $data['slug'] ?? $slug;
        return redirect()->to('/projects/' . $newSlug)->with('success', 'Proje başarıyla güncellendi!');
    }

    /**
     * Delete project
     */
    public function delete(string $slug)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Giriş yapmalısınız.']);
        }

        $project = $this->projectModel->findBySlug($slug);

        if (!$project || $project['user_id'] !== $this->currentUser['id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Yetkisiz işlem.']);
        }

        // Delete screenshot
        if ($project['screenshot'] && file_exists(FCPATH . $project['screenshot'])) {
            unlink(FCPATH . $project['screenshot']);
        }

        $this->projectModel->delete($project['id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Proje silindi.']);
        }

        return redirect()->to('/user/' . $this->currentUser['id'])->with('success', 'Proje başarıyla silindi!');
    }

    /**
     * Enrich projects with likes count and AI tools
     */
    private function enrichProjects(array &$projects): void
    {
        foreach ($projects as &$project) {
            $project['likes_count'] = $this->likeModel->getCountByProject($project['id']);
            $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);

            if ($this->isLoggedIn()) {
                $project['is_liked'] = $this->likeModel->hasLiked($this->currentUser['id'], $project['id']);
            } else {
                $project['is_liked'] = false;
            }
        }
    }
}
