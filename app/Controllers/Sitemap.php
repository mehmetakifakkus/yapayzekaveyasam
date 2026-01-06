<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CategoryModel;
use App\Models\AiToolModel;
use App\Models\TagModel;
use App\Models\UserModel;

class Sitemap extends BaseController
{
    /**
     * Generate sitemap.xml
     */
    public function index()
    {
        $projectModel = model('ProjectModel');
        $categoryModel = model('CategoryModel');
        $aiToolModel = model('AiToolModel');
        $tagModel = model('TagModel');
        $userModel = model('UserModel');

        // Get all approved projects
        $projects = $projectModel
            ->select('slug, updated_at')
            ->where('status', 'approved')
            ->findAll();

        // Get all categories
        $categories = $categoryModel->select('slug')->findAll();

        // Get all AI tools
        $aiTools = $aiToolModel->select('slug')->findAll();

        // Get all tags that have projects
        $tags = $tagModel
            ->select('tags.slug')
            ->join('project_tags', 'project_tags.tag_id = tags.id')
            ->groupBy('tags.id')
            ->findAll();

        // Get all users with approved projects
        $users = $userModel
            ->select('users.id')
            ->join('projects', 'projects.user_id = users.id')
            ->where('projects.status', 'approved')
            ->groupBy('users.id')
            ->findAll();

        $baseUrl = rtrim(base_url(), '/');

        // Build sitemap
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= $this->urlEntry($baseUrl . '/', date('Y-m-d'), 'daily', '1.0');

        // Projects listing
        $xml .= $this->urlEntry($baseUrl . '/projects', date('Y-m-d'), 'daily', '0.9');

        // Individual projects
        foreach ($projects as $project) {
            $lastmod = date('Y-m-d', strtotime($project['updated_at']));
            $xml .= $this->urlEntry($baseUrl . '/projects/' . $project['slug'], $lastmod, 'weekly', '0.8');
        }

        // Categories
        foreach ($categories as $category) {
            $xml .= $this->urlEntry($baseUrl . '/category/' . $category['slug'], date('Y-m-d'), 'weekly', '0.7');
        }

        // AI Tools
        foreach ($aiTools as $tool) {
            $xml .= $this->urlEntry($baseUrl . '/tool/' . $tool['slug'], date('Y-m-d'), 'weekly', '0.7');
        }

        // Tags
        foreach ($tags as $tag) {
            $xml .= $this->urlEntry($baseUrl . '/tag/' . $tag['slug'], date('Y-m-d'), 'weekly', '0.6');
        }

        // User profiles
        foreach ($users as $user) {
            $xml .= $this->urlEntry($baseUrl . '/user/' . $user['id'], date('Y-m-d'), 'weekly', '0.5');
        }

        $xml .= '</urlset>';

        return $this->response
            ->setHeader('Content-Type', 'application/xml')
            ->setBody($xml);
    }

    /**
     * Generate robots.txt
     */
    public function robots()
    {
        $baseUrl = rtrim(base_url(), '/');

        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /auth/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "\n";
        $robots .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setBody($robots);
    }

    /**
     * Helper to create URL entry
     */
    private function urlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n" .
               "    <loc>" . htmlspecialchars($loc) . "</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }
}
