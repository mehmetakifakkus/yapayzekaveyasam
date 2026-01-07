<?php

namespace App\Models;

use CodeIgniter\Model;

class BadgeModel extends Model
{
    protected $table            = 'badges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'slug',
        'name',
        'description',
        'icon',
        'threshold',
        'category',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    /**
     * Get all badges
     */
    public function getAll(): array
    {
        return $this->orderBy('category', 'ASC')
            ->orderBy('threshold', 'ASC')
            ->findAll();
    }

    /**
     * Get badges by category
     */
    public function getByCategory(string $category): array
    {
        return $this->where('category', $category)
            ->orderBy('threshold', 'ASC')
            ->findAll();
    }

    /**
     * Find badge by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
