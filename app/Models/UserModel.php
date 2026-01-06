<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'google_id',
        'name',
        'email',
        'avatar',
        'bio',
        'theme',
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
        'name'  => 'required|min_length[2]|max_length[255]',
        'email' => 'required|valid_email|max_length[255]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Find user by Google ID
     */
    public function findByGoogleId(string $googleId): ?array
    {
        return $this->where('google_id', $googleId)->first();
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get user's projects count
     */
    public function getProjectsCount(int $userId): int
    {
        return $this->db->table('projects')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->countAllResults();
    }

    /**
     * Get user's total likes received
     */
    public function getTotalLikesReceived(int $userId): int
    {
        return $this->db->table('likes')
            ->join('projects', 'projects.id = likes.project_id')
            ->where('projects.user_id', $userId)
            ->countAllResults();
    }
}
