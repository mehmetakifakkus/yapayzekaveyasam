<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <?php if (isset($currentCategory)): ?>
            <h1 class="text-3xl font-bold text-white mb-2"><?= esc($currentCategory['name']) ?></h1>
            <p class="text-slate-400">Bu kategorideki projeler</p>
        <?php elseif (isset($currentTool)): ?>
            <h1 class="text-3xl font-bold text-white mb-2"><?= esc($currentTool['name']) ?> ile Yapılmış</h1>
            <p class="text-slate-400">Bu AI aracı kullanılarak yapılmış projeler</p>
        <?php elseif (!empty($filters['search'])): ?>
            <h1 class="text-3xl font-bold text-white mb-2">"<?= esc($filters['search']) ?>" için sonuçlar</h1>
            <p class="text-slate-400"><?= number_format($totalProjects) ?> proje bulundu</p>
        <?php else: ?>
            <h1 class="text-3xl font-bold text-white mb-2">Tüm Projeler</h1>
            <p class="text-slate-400"><?= number_format($totalProjects) ?> proje</p>
        <?php endif; ?>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="glass-card p-5 sticky top-24">
                <h3 class="text-lg font-semibold text-white mb-4">Filtrele</h3>

                <!-- Sort -->
                <div class="mb-6">
                    <label class="text-sm text-slate-400 mb-2 block">Sıralama</label>
                    <div class="flex flex-wrap gap-2">
                        <a href="?sort=newest<?= isset($currentCategory) ? '&category=' . esc($currentCategory['slug']) : '' ?><?= isset($currentTool) ? '&ai_tool=' . esc($currentTool['slug']) : '' ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="filter-chip <?= ($filters['sort'] ?? 'newest') === 'newest' ? 'active' : '' ?>">
                            En Yeni
                        </a>
                        <a href="?sort=trending<?= isset($currentCategory) ? '&category=' . esc($currentCategory['slug']) : '' ?><?= isset($currentTool) ? '&ai_tool=' . esc($currentTool['slug']) : '' ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="filter-chip <?= ($filters['sort'] ?? '') === 'trending' ? 'active' : '' ?>">
                            Trend
                        </a>
                        <a href="?sort=popular<?= isset($currentCategory) ? '&category=' . esc($currentCategory['slug']) : '' ?><?= isset($currentTool) ? '&ai_tool=' . esc($currentTool['slug']) : '' ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="filter-chip <?= ($filters['sort'] ?? '') === 'popular' ? 'active' : '' ?>">
                            Popüler
                        </a>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-6">
                    <label class="text-sm text-slate-400 mb-2 block">Kategoriler</label>
                    <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                        <a href="<?= base_url('projects') ?>?sort=<?= esc($filters['sort'] ?? 'newest') ?>"
                           class="block px-3 py-2 rounded-lg text-sm <?= !isset($currentCategory) && empty($filters['category']) ? 'bg-purple-500/20 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' ?> transition-colors">
                            Tümü
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?= base_url('category/' . esc($cat['slug'])) ?>?sort=<?= esc($filters['sort'] ?? 'newest') ?>"
                           class="block px-3 py-2 rounded-lg text-sm <?= (isset($currentCategory) && $currentCategory['id'] === $cat['id']) || ($filters['category'] ?? '') === $cat['slug'] ? 'bg-purple-500/20 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' ?> transition-colors">
                            <?= esc($cat['name']) ?>
                            <span class="text-xs text-slate-500">(<?= $cat['project_count'] ?? 0 ?>)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- AI Tools -->
                <div>
                    <label class="text-sm text-slate-400 mb-2 block">AI Araçları</label>
                    <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                        <a href="<?= base_url('projects') ?>?sort=<?= esc($filters['sort'] ?? 'newest') ?>"
                           class="block px-3 py-2 rounded-lg text-sm <?= !isset($currentTool) && empty($filters['ai_tool']) ? 'bg-purple-500/20 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' ?> transition-colors">
                            Tümü
                        </a>
                        <?php foreach ($aiTools as $tool): ?>
                        <a href="<?= base_url('tool/' . esc($tool['slug'])) ?>?sort=<?= esc($filters['sort'] ?? 'newest') ?>"
                           class="block px-3 py-2 rounded-lg text-sm <?= (isset($currentTool) && $currentTool['id'] === $tool['id']) || ($filters['ai_tool'] ?? '') === $tool['slug'] ? 'bg-purple-500/20 text-purple-300' : 'text-slate-400 hover:text-white hover:bg-slate-700/50' ?> transition-colors">
                            <?= esc($tool['name']) ?>
                            <span class="text-xs text-slate-500">(<?= $tool['project_count'] ?? 0 ?>)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Projects Grid -->
        <div class="flex-1">
            <?php if (empty($projects)): ?>
                <div class="glass-card p-12 text-center">
                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">Proje Bulunamadı</h3>
                    <p class="text-slate-400 mb-6">Bu kriterlere uygun proje henüz yok.</p>
                    <a href="<?= base_url('projects/create') ?>" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        İlk Projeyi Ekle
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($projects as $project): ?>
                        <?= view('components/project_card', ['project' => $project]) ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-center gap-2 mt-12">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>&sort=<?= esc($filters['sort'] ?? 'newest') ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="p-2 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    ?>

                    <?php if ($start > 1): ?>
                        <a href="?page=1&sort=<?= esc($filters['sort'] ?? 'newest') ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="w-10 h-10 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition-colors">
                            1
                        </a>
                        <?php if ($start > 2): ?>
                            <span class="text-slate-500">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <a href="?page=<?= $i ?>&sort=<?= esc($filters['sort'] ?? 'newest') ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="w-10 h-10 rounded-lg <?= $i === $currentPage ? 'bg-purple-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700' ?> flex items-center justify-center transition-colors">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <span class="text-slate-500">...</span>
                        <?php endif; ?>
                        <a href="?page=<?= $totalPages ?>&sort=<?= esc($filters['sort'] ?? 'newest') ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="w-10 h-10 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition-colors">
                            <?= $totalPages ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>&sort=<?= esc($filters['sort'] ?? 'newest') ?><?= !empty($filters['search']) ? '&q=' . esc($filters['search']) : '' ?>"
                           class="p-2 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 2px;
}
</style>
