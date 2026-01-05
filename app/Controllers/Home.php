<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CategoryModel;
use App\Models\AiToolModel;

class Home extends BaseController
{
    protected ProjectModel $projectModel;
    protected CategoryModel $categoryModel;
    protected AiToolModel $aiToolModel;

    public function __construct()
    {
        $this->projectModel = model('ProjectModel');
        $this->categoryModel = model('CategoryModel');
        $this->aiToolModel = model('AiToolModel');
    }

    public function index()
    {
        $userId = $this->isLoggedIn() ? $this->currentUser['id'] : null;

        // Get featured projects (optimized - includes likes_count and ai_tools)
        $featuredProjects = $this->projectModel->getFeaturedWithStats(6, $userId);

        // Get trending projects (optimized)
        $trendingProjects = $this->projectModel->getProjectsWithStats(8, 0, ['sort' => 'trending'], $userId);

        // Get newest projects (optimized)
        $newestProjects = $this->projectModel->getProjectsWithStats(8, 0, ['sort' => 'newest'], $userId);

        // Get categories with counts
        $categories = $this->categoryModel->getAllWithProjectCount();

        // Get AI tools with counts
        $aiTools = $this->aiToolModel->getAllWithProjectCount();

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
}
