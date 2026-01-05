<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CategoryModel;
use App\Models\AiToolModel;
use App\Models\LikeModel;

class Home extends BaseController
{
    protected ProjectModel $projectModel;
    protected CategoryModel $categoryModel;
    protected AiToolModel $aiToolModel;
    protected LikeModel $likeModel;

    public function __construct()
    {
        $this->projectModel = model('ProjectModel');
        $this->categoryModel = model('CategoryModel');
        $this->aiToolModel = model('AiToolModel');
        $this->likeModel = model('LikeModel');
    }

    public function index()
    {
        // Get featured projects
        $featuredProjects = $this->projectModel->getFeaturedProjects(6);

        // Get trending projects (most liked in last 7 days)
        $trendingProjects = $this->projectModel->getApprovedProjects(8, 0, ['sort' => 'trending']);

        // Get newest projects
        $newestProjects = $this->projectModel->getApprovedProjects(8, 0, ['sort' => 'newest']);

        // Get categories with counts
        $categories = $this->categoryModel->getAllWithProjectCount();

        // Get AI tools with counts
        $aiTools = $this->aiToolModel->getAllWithProjectCount();

        // Add like counts and AI tools to projects
        $this->enrichProjects($featuredProjects);
        $this->enrichProjects($trendingProjects);
        $this->enrichProjects($newestProjects);

        // Get total stats
        $totalProjects = $this->projectModel->getApprovedCount();
        $db = \Config\Database::connect();
        $totalUsers = $db->table('users')->countAllResults();

        return view('pages/home', $this->getViewData([
            'title'            => 'AI Showcase - Yapay Zeka ile Yapılmış Projeler',
            'featuredProjects' => $featuredProjects,
            'trendingProjects' => $trendingProjects,
            'newestProjects'   => $newestProjects,
            'categories'       => $categories,
            'aiTools'          => $aiTools,
            'totalProjects'    => $totalProjects,
            'totalUsers'       => $totalUsers,
        ]));
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
