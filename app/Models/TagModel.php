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
    public function findOrCreate(string $name): array
    {
        $slug = url_title($name, '-', true);

        $tag = $this->where('slug', $slug)->first();

        if (!$tag) {
            $this->insert([
                'name' => $name,
                'slug' => $slug,
            ]);
            $tag = $this->find($this->getInsertID());
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

        // Add new tags
        foreach ($tagNames as $tagName) {
            if (!is_string($tagName)) continue;
            $tagName = trim($tagName);
            if (empty($tagName)) continue;

            $tag = $this->findOrCreate($tagName);

            $db->table('project_tags')->insert([
                'project_id' => (int) $projectId,
                'tag_id'     => (int) $tag['id'],
            ]);
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
