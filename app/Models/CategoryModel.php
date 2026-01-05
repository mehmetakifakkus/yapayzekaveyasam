<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'icon',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all categories with project count
     */
    public function getAllWithProjectCount(): array
    {
        return $this->select('categories.*, COUNT(projects.id) as project_count')
            ->join('projects', 'projects.category_id = categories.id AND projects.status = "approved"', 'left')
            ->groupBy('categories.id')
            ->orderBy('categories.name', 'ASC')
            ->findAll();
    }
}
