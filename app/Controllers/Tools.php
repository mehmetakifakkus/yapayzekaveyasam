<?php

namespace App\Controllers;

use App\Models\AiToolModel;
use App\Models\ProjectModel;
use App\Models\CategoryModel;
use App\Models\LikeModel;

class Tools extends BaseController
{
    protected AiToolModel $aiToolModel;
    protected ProjectModel $projectModel;
    protected CategoryModel $categoryModel;
    protected LikeModel $likeModel;

    public function __construct()
    {
        $this->aiToolModel = model('AiToolModel');
        $this->projectModel = model('ProjectModel');
        $this->categoryModel = model('CategoryModel');
        $this->likeModel = model('LikeModel');
    }

    /**
     * Show projects using a specific AI tool
     */
    public function show(string $slug)
    {
        $aiTool = $this->aiToolModel->findBySlug($slug);

        if (!$aiTool) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('AI Aracı bulunamadı.');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'ai_tool' => $slug,
            'sort'    => $this->request->getGet('sort') ?? 'newest',
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
            'title'         => $aiTool['name'] . ' ile Yapılmış Projeler - AI Showcase',
            'projects'      => $projects,
            'categories'    => $categories,
            'aiTools'       => $aiTools,
            'filters'       => $filters,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalProjects' => $totalProjects,
            'currentTool'   => $aiTool,
        ]));
    }
}
