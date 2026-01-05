<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'website_url',
        'github_url',
        'screenshot',
        'views',
        'is_featured',
        'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'title'       => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'website_url' => 'required|valid_url',
        'category_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Find project by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->select('projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name, categories.slug as category_slug')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('projects.slug', $slug)
            ->where('projects.status', 'approved')
            ->first();
    }

    /**
     * Get all approved projects with user and category info
     */
    public function getApprovedProjects(int $limit = 12, int $offset = 0, array $filters = []): array
    {
        $builder = $this->select('projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name, categories.slug as category_slug')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('projects.status', 'approved');

        // Category filter
        if (!empty($filters['category'])) {
            $builder->where('categories.slug', $filters['category']);
        }

        // AI Tool filter
        if (!empty($filters['ai_tool'])) {
            $builder->join('project_ai_tools', 'project_ai_tools.project_id = projects.id')
                ->join('ai_tools', 'ai_tools.id = project_ai_tools.ai_tool_id')
                ->where('ai_tools.slug', $filters['ai_tool']);
        }

        // Search filter
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('projects.title', $filters['search'])
                ->orLike('projects.description', $filters['search'])
                ->groupEnd();
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'popular':
                $builder->orderBy('projects.views', 'DESC');
                break;
            case 'trending':
                // Trending: likes in last 7 days
                $builder->select('(SELECT COUNT(*) FROM likes WHERE likes.project_id = projects.id AND likes.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as recent_likes', false)
                    ->orderBy('recent_likes', 'DESC');
                break;
            case 'oldest':
                $builder->orderBy('projects.created_at', 'ASC');
                break;
            default: // newest
                $builder->orderBy('projects.created_at', 'DESC');
        }

        return $builder->limit($limit, $offset)->findAll();
    }

    /**
     * Get featured projects
     */
    public function getFeaturedProjects(int $limit = 6): array
    {
        return $this->select('projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('projects.status', 'approved')
            ->where('projects.is_featured', 1)
            ->orderBy('projects.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get projects by user ID
     */
    public function getByUserId(int $userId): array
    {
        return $this->select('projects.*, categories.name as category_name')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('projects.user_id', $userId)
            ->orderBy('projects.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Increment view count
     */
    public function incrementViews(int $projectId): bool
    {
        return $this->set('views', 'views + 1', false)
            ->where('id', $projectId)
            ->update();
    }

    /**
     * Get likes count for a project
     */
    public function getLikesCount(int $projectId): int
    {
        return $this->db->table('likes')
            ->where('project_id', $projectId)
            ->countAllResults();
    }

    /**
     * Check if user liked a project
     */
    public function isLikedByUser(int $projectId, int $userId): bool
    {
        return $this->db->table('likes')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;
    }

    /**
     * Get total count of approved projects
     */
    public function getApprovedCount(array $filters = []): int
    {
        $builder = $this->where('status', 'approved');

        if (!empty($filters['category'])) {
            $builder->join('categories', 'categories.id = projects.category_id')
                ->where('categories.slug', $filters['category']);
        }

        if (!empty($filters['ai_tool'])) {
            $builder->join('project_ai_tools', 'project_ai_tools.project_id = projects.id')
                ->join('ai_tools', 'ai_tools.id = project_ai_tools.ai_tool_id')
                ->where('ai_tools.slug', $filters['ai_tool']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('projects.title', $filters['search'])
                ->orLike('projects.description', $filters['search'])
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Generate unique slug
     */
    public function generateSlug(string $title): string
    {
        $slug = url_title($title, '-', true);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
