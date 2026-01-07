<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProjectModel;
use App\Models\WeeklyHighlightModel;

class UpdateWeeklyHighlights extends BaseCommand
{
    protected $group       = 'Highlights';
    protected $name        = 'highlights:update';
    protected $description = 'Update weekly highlights with top projects from the past week';
    protected $usage       = 'highlights:update [--dry-run]';
    protected $arguments   = [];
    protected $options     = [
        '--dry-run' => 'Preview without saving to database',
    ];

    public function run(array $params)
    {
        $dryRun = CLI::getOption('dry-run') ?? false;

        CLI::write('Haftalık Öne Çıkanlar Güncelleniyor...', 'yellow');
        CLI::newLine();

        $projectModel = model('ProjectModel');
        $highlightModel = model('WeeklyHighlightModel');

        // Get week dates
        $weekStart = $highlightModel->getWeekStart();
        $weekEnd = $highlightModel->getWeekEnd();

        CLI::write("Hafta: {$weekStart} - {$weekEnd}", 'light_gray');
        CLI::newLine();

        // Get top projects from the past 7 days
        $topProjects = $projectModel->getTopProjectsByLikesInPeriod(7, 6);

        if (empty($topProjects)) {
            CLI::write('Son 7 günde beğeni alan proje bulunamadı.', 'yellow');
            return;
        }

        CLI::write('Bu Haftanın Yıldızları:', 'green');
        CLI::newLine();

        $rank = 1;
        foreach ($topProjects as $project) {
            $likes = $project['week_likes'] ?? 0;
            CLI::write("  {$rank}. {$project['title']}", 'cyan');
            CLI::write("     - {$likes} beğeni (bu hafta)", 'light_gray');
            CLI::write("     - {$project['user_name']}", 'light_gray');
            $rank++;
        }

        CLI::newLine();

        if ($dryRun) {
            CLI::write('DRY RUN: Değişiklikler kaydedilmedi.', 'yellow');
            return;
        }

        // Save highlights
        $highlightModel->saveWeekHighlights($topProjects, $weekStart, $weekEnd);

        // Update is_featured field
        $db = \Config\Database::connect();

        // Clear old featured projects
        $db->table('projects')->update(['is_featured' => 0]);

        // Set new featured projects
        $projectIds = array_column($topProjects, 'id');
        if (!empty($projectIds)) {
            $db->table('projects')
                ->whereIn('id', $projectIds)
                ->update(['is_featured' => 1]);
        }

        CLI::write('Haftalık öne çıkanlar güncellendi!', 'green');
        CLI::write("Toplam {$rank} proje öne çıkarıldı.", 'light_gray');
    }
}
