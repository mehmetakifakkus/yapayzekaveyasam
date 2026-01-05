<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AiToolSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Claude Code',
                'slug'        => 'claude-code',
                'icon'        => null,
                'website_url' => 'https://claude.ai',
                'color'       => '#D97706',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Cursor',
                'slug'        => 'cursor',
                'icon'        => null,
                'website_url' => 'https://cursor.sh',
                'color'       => '#000000',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Windsurf',
                'slug'        => 'windsurf',
                'icon'        => null,
                'website_url' => 'https://codeium.com/windsurf',
                'color'       => '#09B6A2',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'GitHub Copilot',
                'slug'        => 'github-copilot',
                'icon'        => null,
                'website_url' => 'https://github.com/features/copilot',
                'color'       => '#6366F1',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'v0',
                'slug'        => 'v0',
                'icon'        => null,
                'website_url' => 'https://v0.dev',
                'color'       => '#000000',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Bolt',
                'slug'        => 'bolt',
                'icon'        => null,
                'website_url' => 'https://bolt.new',
                'color'       => '#3B82F6',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Lovable',
                'slug'        => 'lovable',
                'icon'        => null,
                'website_url' => 'https://lovable.dev',
                'color'       => '#EC4899',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Replit AI',
                'slug'        => 'replit-ai',
                'icon'        => null,
                'website_url' => 'https://replit.com',
                'color'       => '#F26207',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Codeium',
                'slug'        => 'codeium',
                'icon'        => null,
                'website_url' => 'https://codeium.com',
                'color'       => '#09B6A2',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'TabNine',
                'slug'        => 'tabnine',
                'icon'        => null,
                'website_url' => 'https://tabnine.com',
                'color'       => '#6B5CE7',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('ai_tools')->insertBatch($data);
    }
}
