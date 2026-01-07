<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        $badges = [
            [
                'slug'        => 'first_project',
                'name'        => 'Yeni Başlangıç',
                'description' => 'İlk projenizi yüklediniz!',
                'icon'        => '🚀',
                'threshold'   => 1,
                'category'    => 'projects',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'slug'        => 'projects_5',
                'name'        => 'Üretken',
                'description' => '5 proje yüklediniz!',
                'icon'        => '⭐',
                'threshold'   => 5,
                'category'    => 'projects',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'slug'        => 'projects_10',
                'name'        => 'Süper Üretken',
                'description' => '10 proje yüklediniz!',
                'icon'        => '🌟',
                'threshold'   => 10,
                'category'    => 'projects',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'slug'        => 'likes_10',
                'name'        => 'Beğenilen',
                'description' => 'Projeleriniz toplam 10 beğeni aldı!',
                'icon'        => '❤️',
                'threshold'   => 10,
                'category'    => 'likes',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'slug'        => 'likes_50',
                'name'        => 'Popüler',
                'description' => 'Projeleriniz toplam 50 beğeni aldı!',
                'icon'        => '💖',
                'threshold'   => 50,
                'category'    => 'likes',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'slug'        => 'likes_100',
                'name'        => 'Süperstar',
                'description' => 'Projeleriniz toplam 100 beğeni aldı!',
                'icon'        => '💝',
                'threshold'   => 100,
                'category'    => 'likes',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'slug'        => 'followers_10',
                'name'        => 'Etkileyici',
                'description' => '10 takipçiye ulaştınız!',
                'icon'        => '👥',
                'threshold'   => 10,
                'category'    => 'followers',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('badges')->insertBatch($badges);
    }
}
