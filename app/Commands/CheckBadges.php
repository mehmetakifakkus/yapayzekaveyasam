<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\BadgeChecker;
use App\Models\UserModel;

class CheckBadges extends BaseCommand
{
    protected $group       = 'Badges';
    protected $name        = 'badges:check';
    protected $description = 'Check and award badges for users';
    protected $usage       = 'badges:check [user_id] [--all]';
    protected $arguments   = [
        'user_id' => 'Specific user ID to check (optional)',
    ];
    protected $options     = [
        '--all' => 'Check all users',
    ];

    public function run(array $params)
    {
        $badgeChecker = new BadgeChecker();
        $userModel = model('UserModel');

        $userId = $params[0] ?? null;
        $checkAll = CLI::getOption('all') ?? false;

        if ($checkAll) {
            CLI::write('Checking badges for all users...', 'yellow');
            $users = $userModel->findAll();
            $totalAwarded = 0;

            foreach ($users as $user) {
                $awarded = $badgeChecker->checkAllBadges($user['id']);
                if (!empty($awarded)) {
                    CLI::write("  {$user['name']}:", 'green');
                    foreach ($awarded as $badge) {
                        CLI::write("    - {$badge['icon']} {$badge['name']}", 'cyan');
                        $totalAwarded++;
                    }
                }
            }

            CLI::newLine();
            CLI::write("Total badges awarded: {$totalAwarded}", 'green');
        } elseif ($userId) {
            $user = $userModel->find($userId);
            if (!$user) {
                CLI::error("User with ID {$userId} not found.");
                return;
            }

            CLI::write("Checking badges for {$user['name']}...", 'yellow');
            $awarded = $badgeChecker->checkAllBadges((int) $userId);

            if (empty($awarded)) {
                CLI::write('No new badges earned.', 'light_gray');
            } else {
                CLI::write('Badges awarded:', 'green');
                foreach ($awarded as $badge) {
                    CLI::write("  - {$badge['icon']} {$badge['name']}", 'cyan');
                }
            }
        } else {
            CLI::error('Please provide a user_id or use --all flag.');
            CLI::write('Usage: php spark badges:check [user_id]', 'yellow');
            CLI::write('       php spark badges:check --all', 'yellow');
        }
    }
}
