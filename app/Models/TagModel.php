<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Find tag by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Find or create tag by name
     */
    public function findOrCreate(string $name): ?array
    {
        helper('text');
        $slug = turkish_slug($name);

        if (empty($slug)) {
            return null;
        }

        $tag = $this->where('slug', $slug)->first();

        if (!$tag) {
            $db = \Config\Database::connect();
            $db->query(
                "INSERT INTO tags (name, slug, created_at) VALUES (?, ?, ?)",
                [$name, $slug, date('Y-m-d H:i:s')]
            );
            $insertId = $db->insertID();
            $tag = $this->find($insertId);
        }

        return $tag;
    }

    /**
     * Get all tags with project count
     */
    public function getAllWithProjectCount(): array
    {
        return $this->select('tags.*, COUNT(DISTINCT project_tags.project_id) as project_count')
            ->join('project_tags', 'project_tags.tag_id = tags.id', 'left')
            ->join('projects', 'projects.id = project_tags.project_id AND projects.status = "approved"', 'left')
            ->groupBy('tags.id')
            ->orderBy('project_count', 'DESC')
            ->findAll();
    }

    /**
     * Get popular tags (with most projects)
     */
    public function getPopular(int $limit = 20): array
    {
        return $this->select('tags.*, COUNT(DISTINCT project_tags.project_id) as project_count')
            ->join('project_tags', 'project_tags.tag_id = tags.id')
            ->join('projects', 'projects.id = project_tags.project_id AND projects.status = "approved"')
            ->groupBy('tags.id')
            ->orderBy('project_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get tags for a specific project
     */
    public function getByProjectId(int $projectId): array
    {
        return $this->select('tags.*')
            ->join('project_tags', 'project_tags.tag_id = tags.id')
            ->where('project_tags.project_id', $projectId)
            ->findAll();
    }

    /**
     * Sync tags for a project
     */
    public function syncProjectTags(int $projectId, array $tagNames): void
    {
        $db = \Config\Database::connect();

        // Remove existing tags
        $db->table('project_tags')->where('project_id', $projectId)->delete();

        // Filter and clean tag names
        $cleanedTags = [];
        foreach ($tagNames as $key => $tagName) {
            if (!is_string($tagName)) continue;
            $tagName = trim($tagName);
            if (empty($tagName) || strlen($tagName) > 50) continue;
            $cleanedTags[] = $tagName;
        }

        // Add new tags
        foreach ($cleanedTags as $tagName) {
            $tag = $this->findOrCreate($tagName);

            if ($tag && isset($tag['id'])) {
                $db->query(
                    "INSERT INTO project_tags (project_id, tag_id) VALUES (?, ?)",
                    [$projectId, (int) $tag['id']]
                );
            }
        }
    }

    /**
     * Search tags by name
     */
    public function search(string $query, int $limit = 10): array
    {
        return $this->like('name', $query)
            ->limit($limit)
            ->findAll();
    }
}
