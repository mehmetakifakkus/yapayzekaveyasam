<?php

namespace App\Models;

use CodeIgniter\Model;

class AiToolModel extends Model
{
    protected $table            = 'ai_tools';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'icon',
        'website_url',
        'color',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Find AI tool by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all AI tools with project count
     */
    public function getAllWithProjectCount(): array
    {
        return $this->select('ai_tools.*, COUNT(DISTINCT project_ai_tools.project_id) as project_count')
            ->join('project_ai_tools', 'project_ai_tools.ai_tool_id = ai_tools.id', 'left')
            ->join('projects', 'projects.id = project_ai_tools.project_id AND projects.status = "approved"', 'left')
            ->groupBy('ai_tools.id')
            ->orderBy('ai_tools.name', 'ASC')
            ->findAll();
    }

    /**
     * Get AI tools for a specific project
     */
    public function getByProjectId(int $projectId): array
    {
        return $this->select('ai_tools.*')
            ->join('project_ai_tools', 'project_ai_tools.ai_tool_id = ai_tools.id')
            ->where('project_ai_tools.project_id', $projectId)
            ->findAll();
    }
}
