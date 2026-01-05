<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative overflow-hidden py-20 lg:py-32">
    <!-- Background gradient -->
    <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/10 via-purple-500/5 to-transparent"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-purple-500/20 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6">
            <span class="text-white">Yapay Zeka ile</span>
            <br>
            <span class="gradient-text">Yapılmış Projeler</span>
        </h1>

        <p class="text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto mb-10">
            Claude Code, Cursor, Windsurf ve diğer AI araçlarıyla oluşturulmuş harika web projelerini keşfedin ve kendi projelerinizi paylaşın.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= base_url('projects') ?>" class="btn-primary text-lg px-8 py-4">
                Projeleri Keşfet
            </a>
            <?php if (!$isLoggedIn): ?>
            <a href="<?= base_url('auth/google') ?>" class="btn-secondary text-lg px-8 py-4 flex items-center gap-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Proje Paylaş
            </a>
            <?php else: ?>
            <a href="<?= base_url('projects/create') ?>" class="btn-secondary text-lg px-8 py-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Proje Paylaş
            </a>
            <?php endif; ?>
        </div>

        <!-- Stats -->
        <div class="flex items-center justify-center gap-8 sm:gap-16 mt-16">
            <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold gradient-text"><?= number_format($totalProjects) ?></div>
                <div class="text-sm text-slate-400 mt-1">Proje</div>
            </div>
            <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold gradient-text"><?= number_format($totalUsers) ?></div>
                <div class="text-sm text-slate-400 mt-1">Geliştirici</div>
            </div>
            <div class="text-center">
                <div class="text-3xl sm:text-4xl font-bold gradient-text"><?= count($aiTools) ?></div>
                <div class="text-sm text-slate-400 mt-1">AI Aracı</div>
            </div>
        </div>
    </div>
</section>

<!-- AI Tools Section -->
<section class="py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-white mb-8 text-center">Desteklenen AI Araçları</h2>

        <div class="flex flex-wrap justify-center gap-4">
            <?php foreach ($aiTools as $tool): ?>
            <a
                href="<?= base_url('tool/' . esc($tool['slug'])) ?>"
                class="ai-badge text-sm py-2 px-4"
                style="<?= $tool['color'] ? 'border-color: ' . esc($tool['color']) . '40;' : '' ?>"
            >
                <?= esc($tool['name']) ?>
                <span class="text-xs text-slate-500">(<?= $tool['project_count'] ?? 0 ?>)</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trending Projects -->
<?php if (!empty($trendingProjects)): ?>
<section class="py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-pink-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Trend Projeler</h2>
            </div>
            <a href="<?= base_url('projects?sort=trending') ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                Tümünü Gör &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($trendingProjects as $project): ?>
                <?= view('components/project_card', ['project' => $project]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newest Projects -->
<?php if (!empty($newestProjects)): ?>
<section class="py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Yeni Eklenenler</h2>
            </div>
            <a href="<?= base_url('projects?sort=newest') ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                Tümünü Gör &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($newestProjects as $project): ?>
                <?= view('components/project_card', ['project' => $project]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Section -->
<section class="py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-white mb-8 text-center">Kategoriler</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            <?php foreach ($categories as $category): ?>
            <a
                href="<?= base_url('category/' . esc($category['slug'])) ?>"
                class="glass-card p-4 text-center hover:border-purple-500/50 transition-all group"
            >
                <div class="text-2xl mb-2">
                    <?php
                    $icons = [
                        'shopping-cart' => '🛒',
                        'chart-bar' => '📊',
                        'briefcase' => '💼',
                        'document-text' => '📝',
                        'users' => '👥',
                        'puzzle' => '🎮',
                        'academic-cap' => '🎓',
                        'currency-dollar' => '💰',
                        'heart' => '❤️',
                        'dots-horizontal' => '📦',
                    ];
                    echo $icons[$category['icon']] ?? '📁';
                    ?>
                </div>
                <h3 class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">
                    <?= esc($category['name']) ?>
                </h3>
                <p class="text-xs text-slate-500 mt-1"><?= $category['project_count'] ?? 0 ?> proje</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 border-t border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
            Projenizi Paylaşın
        </h2>
        <p class="text-lg text-slate-400 mb-8">
            AI araçlarıyla yaptığınız harika projeleri dünyayla paylaşın ve topluluktan geri bildirim alın.
        </p>

        <?php if (!$isLoggedIn): ?>
        <a href="<?= base_url('auth/google') ?>" class="btn-primary text-lg px-8 py-4 inline-flex items-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google ile Başla
        </a>
        <?php else: ?>
        <a href="<?= base_url('projects/create') ?>" class="btn-primary text-lg px-8 py-4 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Proje Ekle
        </a>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
