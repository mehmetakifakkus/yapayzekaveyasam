<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-white mb-6">Takip Akışı</h1>

            <?php if (empty($projects)): ?>
                <div class="glass-card p-12 text-center">
                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">Henüz İçerik Yok</h3>
                    <p class="text-slate-400 mb-6">Başkalarını takip edin ve projelerini burada görün.</p>
                    <a href="<?= base_url('projects') ?>" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Projeleri Keşfet
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($projects as $project): ?>
                        <?= view('components/project_card', ['project' => $project]) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="glass-card p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-white mb-4">Takip Ettikleriniz</h2>

                <?php if (empty($followingUsers)): ?>
                    <p class="text-slate-400 text-sm">Henüz kimseyi takip etmiyorsunuz.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($followingUsers as $followedUser): ?>
                            <a href="<?= base_url('user/' . $followedUser['id']) ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-700/50 transition-colors">
                                <?php if (!empty($followedUser['avatar'])): ?>
                                    <img src="<?= esc($followedUser['avatar']) ?>" alt="" class="w-10 h-10 rounded-full">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium">
                                        <?= strtoupper(substr($followedUser['name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate"><?= esc($followedUser['name']) ?></p>
                                    <?php if (!empty($followedUser['bio'])): ?>
                                        <p class="text-xs text-slate-500 truncate"><?= esc(character_limiter($followedUser['bio'], 30)) ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr class="border-slate-700/50 my-4">

                <a href="<?= base_url('projects') ?>" class="text-sm text-purple-400 hover:text-purple-300 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Yeni kişiler keşfet
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
