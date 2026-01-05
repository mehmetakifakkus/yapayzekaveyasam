<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <nav class="text-sm text-slate-500 mb-2">
                <a href="<?= base_url('user/' . $user['id']) ?>" class="hover:text-purple-400">Profil</a>
                <span class="mx-2">/</span>
                <span class="text-slate-300">Kaydedilenler</span>
            </nav>
            <h1 class="text-2xl font-bold text-white">Kaydedilen Projeler</h1>
        </div>
    </div>

    <?php if (empty($projects)): ?>
        <div class="glass-card p-12 text-center">
            <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-white mb-2">Henüz Kayıt Yok</h3>
            <p class="text-slate-400 mb-6">Beğendiğiniz projeleri kaydedin ve daha sonra kolayca erişin.</p>
            <a href="<?= base_url('projects') ?>" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Projeleri Keşfet
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($projects as $project): ?>
                <?= view('components/project_card', ['project' => $project]) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
