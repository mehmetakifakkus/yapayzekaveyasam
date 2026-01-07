<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MailgunService;
use App\Models\UserModel;
use App\Models\FollowModel;
use App\Models\ProjectModel;
use App\Models\AiToolModel;

class SendWeeklyDigest extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'digest:send';
    protected $description = 'Send weekly digest emails to users about new projects from followed users';
    protected $usage       = 'digest:send [--dry-run]';
    protected $arguments   = [];
    protected $options     = [
        '--dry-run' => 'Preview without sending emails',
    ];

    public function run(array $params)
    {
        $dryRun = CLI::getOption('dry-run') ?? false;

        CLI::write('Starting weekly digest...', 'yellow');

        $mailgun = new MailgunService();

        if (!$mailgun->isConfigured() && !$dryRun) {
            CLI::error('Mailgun is not configured. Please set MAILGUN_API_KEY and MAILGUN_DOMAIN in .env');
            return;
        }

        $userModel = model('UserModel');
        $followModel = model('FollowModel');
        $projectModel = model('ProjectModel');
        $aiToolModel = model('AiToolModel');

        // Get users who want email digest
        $users = $userModel->where('email_digest', 1)
            ->where('is_banned', 0)
            ->findAll();

        CLI::write('Found ' . count($users) . ' users with digest enabled', 'green');

        $sent = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($users as $user) {
            // Get users they follow
            $followingIds = $followModel->getFollowingIds($user['id']);

            if (empty($followingIds)) {
                CLI::write("  Skipping {$user['name']} - not following anyone", 'light_gray');
                $skipped++;
                continue;
            }

            // Get new projects from followed users in the last 7 days
            $projects = $projectModel
                ->select('projects.*, users.name as user_name, users.avatar as user_avatar, categories.name as category_name')
                ->join('users', 'users.id = projects.user_id')
                ->join('categories', 'categories.id = projects.category_id')
                ->whereIn('projects.user_id', $followingIds)
                ->where('projects.status', 'approved')
                ->where('projects.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->orderBy('projects.created_at', 'DESC')
                ->limit(10)
                ->findAll();

            if (empty($projects)) {
                CLI::write("  Skipping {$user['name']} - no new projects", 'light_gray');
                $skipped++;
                continue;
            }

            // Enrich projects with AI tools
            foreach ($projects as &$project) {
                $project['ai_tools'] = $aiToolModel->getByProjectId($project['id']);
            }

            // Generate email HTML
            $html = view('emails/weekly_digest', [
                'user'     => $user,
                'projects' => $projects,
                'baseUrl'  => base_url(),
            ]);

            if ($dryRun) {
                CLI::write("  Would send to {$user['name']} ({$user['email']}) - " . count($projects) . " projects", 'cyan');
            } else {
                $result = $mailgun->send(
                    $user['email'],
                    'Haftalık Özet: Takip Ettiklerinizden Yeni Projeler',
                    $html
                );

                if ($result['success']) {
                    CLI::write("  Sent to {$user['name']} ({$user['email']})", 'green');
                    $sent++;
                } else {
                    CLI::write("  Failed for {$user['email']}: {$result['message']}", 'red');
                    $errors++;
                }

                // Rate limiting
                usleep(200000);
            }
        }

        CLI::newLine();
        CLI::write('Weekly digest complete!', 'yellow');
        CLI::write("Sent: {$sent}, Skipped: {$skipped}, Errors: {$errors}", 'green');
    }
}
