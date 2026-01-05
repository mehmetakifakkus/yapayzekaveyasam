<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProjectModel;
use App\Models\LikeModel;
use App\Models\AiToolModel;

class Categories extends BaseController
{
    protected CategoryModel $categoryModel;
    protected ProjectModel $projectModel;
    protected LikeModel $likeModel;
    protected AiToolModel $aiToolModel;

    public function __construct()
    {
        $this->categoryModel = model('CategoryModel');
        $this->projectModel = model('ProjectModel');
        $this->likeModel = model('LikeModel');
        $this->aiToolModel = model('AiToolModel');
    }

    /**
     * Show projects in a category
     */
    public function show(string $slug)
    {
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori bulunamadı.');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'category' => $slug,
            'sort'     => $this->request->getGet('sort') ?? 'newest',
        ];

        $projects = $this->projectModel->getApprovedProjects($perPage, $offset, $filters);
        $totalProjects = $this->projectModel->getApprovedCount($filters);
        $totalPages = ceil($totalProjects / $perPage);

        // Enrich projects
        foreach ($projects as &$project) {
            $project['likes_count'] = $this->likeModel->getCountByProject($project['id']);
            $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);
            $project['is_liked'] = $this->isLoggedIn()
                ? $this->likeModel->hasLiked($this->currentUser['id'], $project['id'])
                : false;
        }

        $categories = $this->categoryModel->getAllWithProjectCount();
        $aiTools = $this->aiToolModel->getAllWithProjectCount();

        return view('pages/projects_list', $this->getViewData([
            'title'           => $category['name'] . ' Projeleri - AI Showcase',
            'projects'        => $projects,
            'categories'      => $categories,
            'aiTools'         => $aiTools,
            'filters'         => $filters,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'totalProjects'   => $totalProjects,
            'currentCategory' => $category,
        ]));
    }
}
