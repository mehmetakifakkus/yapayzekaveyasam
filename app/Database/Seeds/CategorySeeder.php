<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'       => 'E-Ticaret',
                'slug'       => 'e-ticaret',
                'icon'       => 'shopping-cart',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'SaaS / Dashboard',
                'slug'       => 'saas-dashboard',
                'icon'       => 'chart-bar',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Portfolio',
                'slug'       => 'portfolio',
                'icon'       => 'briefcase',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Blog / CMS',
                'slug'       => 'blog-cms',
                'icon'       => 'document-text',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Sosyal Medya',
                'slug'       => 'sosyal-medya',
                'icon'       => 'users',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Oyun',
                'slug'       => 'oyun',
                'icon'       => 'puzzle',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Eğitim',
                'slug'       => 'egitim',
                'icon'       => 'academic-cap',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Finans',
                'slug'       => 'finans',
                'icon'       => 'currency-dollar',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Sağlık',
                'slug'       => 'saglik',
                'icon'       => 'heart',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Diğer',
                'slug'       => 'diger',
                'icon'       => 'dots-horizontal',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
