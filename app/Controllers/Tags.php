<?php

namespace App\Controllers;

use App\Models\TagModel;
use App\Models\ProjectModel;
use App\Models\LikeModel;
use App\Models\AiToolModel;
use App\Models\CategoryModel;

class Tags extends BaseController
{
    protected TagModel $tagModel;
    protected ProjectModel $projectModel;
    protected LikeModel $likeModel;
    protected AiToolModel $aiToolModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->tagModel = model('TagModel');
        $this->projectModel = model('ProjectModel');
        $this->likeModel = model('LikeModel');
        $this->aiToolModel = model('AiToolModel');
        $this->categoryModel = model('CategoryModel');
    }

    /**
     * Show projects with a specific tag
     */
    public function show(string $slug)
    {
        $tag = $this->tagModel->findBySlug($slug);

        if (!$tag) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Etiket bulunamadı.');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'tag'  => $slug,
            'sort' => $this->request->getGet('sort') ?? 'newest',
        ];

        $projects = $this->projectModel->getApprovedProjects($perPage, $offset, $filters);
        $totalProjects = $this->projectModel->getApprovedCount($filters);
        $totalPages = ceil($totalProjects / $perPage);

        // Enrich projects
        foreach ($projects as &$project) {
            $project['likes_count'] = $this->likeModel->getCountByProject($project['id']);
            $project['ai_tools'] = $this->aiToolModel->getByProjectId($project['id']);
            $project['tags'] = $this->tagModel->getByProjectId($project['id']);
            $project['is_liked'] = $this->isLoggedIn()
                ? $this->likeModel->hasLiked($this->currentUser['id'], $project['id'])
                : false;
        }

        $categories = $this->categoryModel->getAllWithProjectCount();
        $aiTools = $this->aiToolModel->getAllWithProjectCount();
        $popularTags = $this->tagModel->getPopular(20);

        return view('pages/projects_list', $this->getViewData([
            'title'         => '#' . $tag['name'] . ' Etiketli Projeler - AI Showcase',
            'projects'      => $projects,
            'categories'    => $categories,
            'aiTools'       => $aiTools,
            'popularTags'   => $popularTags,
            'filters'       => $filters,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalProjects' => $totalProjects,
            'currentTag'    => $tag,
        ]));
    }

    /**
     * Search tags (for autocomplete)
     */
    public function search()
    {
        $query = $this->request->getGet('q') ?? '';

        if (strlen($query) < 2) {
            return $this->response->setJSON(['tags' => []]);
        }

        $tags = $this->tagModel->search($query, 10);

        return $this->response->setJSON(['tags' => $tags]);
    }
}
