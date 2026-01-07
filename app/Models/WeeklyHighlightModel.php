<?php

namespace App\Models;

use CodeIgniter\Model;

class WeeklyHighlightModel extends Model
{
    protected $table            = 'weekly_highlights';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id',
        'week_start',
        'week_end',
        'likes_count',
        'rank',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Get current week's highlights with full project data
     */
    public function getCurrentWeekHighlights(?int $userId = null): array
    {
        $weekStart = $this->getWeekStart();

        return $this->getWeekHighlights($weekStart, $userId);
    }

    /**
     * Get highlights for a specific week
     */
    public function getWeekHighlights(string $weekStart, ?int $userId = null): array
    {
        $highlights = $this->select('weekly_highlights.*, projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name, categories.slug as category_slug')
            ->join('projects', 'projects.id = weekly_highlights.project_id')
            ->join('users', 'users.id = projects.user_id')
            ->join('categories', 'categories.id = projects.category_id')
            ->where('weekly_highlights.week_start', $weekStart)
            ->where('projects.status', 'approved')
            ->orderBy('weekly_highlights.rank', 'ASC')
            ->findAll();

        if (empty($highlights)) {
            return [];
        }

        // Get AI tools for all projects
        $projectIds = array_column($highlights, 'project_id');
        $aiTools = $this->getAiToolsForProjects($projectIds);

        // Get user's liked projects
        $likedProjectIds = [];
        if ($userId) {
            $likedProjectIds = $this->db->table('likes')
                ->select('project_id')
                ->where('user_id', $userId)
                ->whereIn('project_id', $projectIds)
                ->get()
                ->getResultArray();
            $likedProjectIds = array_column($likedProjectIds, 'project_id');
        }

        // Enrich projects
        foreach ($highlights as &$highlight) {
            $highlight['ai_tools'] = $aiTools[$highlight['project_id']] ?? [];
            $highlight['is_liked'] = in_array($highlight['project_id'], $likedProjectIds);
            // Use the stored likes_count from weekly_highlights
            $highlight['id'] = $highlight['project_id']; // For compatibility with project_card
        }

        return $highlights;
    }

    /**
     * Get archive of past weeks
     */
    public function getArchive(int $limit = 10): array
    {
        return $this->select('week_start, week_end, COUNT(*) as project_count')
            ->groupBy('week_start')
            ->orderBy('week_start', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Save week highlights
     */
    public function saveWeekHighlights(array $projects, string $weekStart, string $weekEnd): bool
    {
        $db = \Config\Database::connect();

        // Delete existing highlights for this week
        $db->table('weekly_highlights')->where('week_start', $weekStart)->delete();

        // Insert new highlights using raw query
        $rank = 1;
        foreach ($projects as $project) {
            $likesCount = (int) ($project['week_likes'] ?? 0);
            $projectId = (int) $project['id'];

            $db->query(
                "INSERT INTO weekly_highlights (project_id, week_start, week_end, likes_count, `rank`, created_at) VALUES (?, ?, ?, ?, ?, ?)",
                [$projectId, $weekStart, $weekEnd, $likesCount, $rank++, date('Y-m-d H:i:s')]
            );
        }

        return true;
    }

    /**
     * Check if current week has highlights
     */
    public function hasCurrentWeekHighlights(): bool
    {
        $weekStart = $this->getWeekStart();
        return $this->where('week_start', $weekStart)->countAllResults() > 0;
    }

    /**
     * Get the start of the current week (Monday)
     */
    public function getWeekStart(?string $date = null): string
    {
        $date = $date ?? date('Y-m-d');
        $timestamp = strtotime($date);
        $dayOfWeek = date('N', $timestamp); // 1 = Monday, 7 = Sunday
        $monday = strtotime('-' . ($dayOfWeek - 1) . ' days', $timestamp);
        return date('Y-m-d', $monday);
    }

    /**
     * Get the end of the current week (Sunday)
     */
    public function getWeekEnd(?string $date = null): string
    {
        $weekStart = $this->getWeekStart($date);
        return date('Y-m-d', strtotime('+6 days', strtotime($weekStart)));
    }

    /**
     * Get AI tools for multiple projects
     */
    private function getAiToolsForProjects(array $projectIds): array
    {
        if (empty($projectIds)) {
            return [];
        }

        $tools = $this->db->table('project_ai_tools')
            ->select('project_ai_tools.project_id, ai_tools.id, ai_tools.name, ai_tools.slug, ai_tools.icon')
            ->join('ai_tools', 'ai_tools.id = project_ai_tools.ai_tool_id')
            ->whereIn('project_ai_tools.project_id', $projectIds)
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($tools as $tool) {
            $pid = $tool['project_id'];
            unset($tool['project_id']);
            $grouped[$pid][] = $tool;
        }

        return $grouped;
    }
}
