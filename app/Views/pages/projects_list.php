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
                <div id="projects-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($projects as $project): ?>
                        <?= view('components/project_card', ['project' => $project]) ?>
                    <?php endforeach; ?>
                </div>

                <!-- Infinite Scroll Loader -->
                <?php if ($totalPages > 1): ?>
                <div id="load-more-container" class="mt-12 text-center" data-page="1" data-total="<?= $totalPages ?>">
                    <div id="load-more-spinner" class="hidden">
                        <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-slate-400 mt-2">Yükleniyor...</p>
                    </div>
                    <button id="load-more-btn" class="btn-secondary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        Daha Fazla Yükle
                    </button>
                    <p id="all-loaded" class="hidden text-slate-500 text-sm">Tüm projeler yüklendi</p>
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

<?= $this->section('scripts') ?>
<script>
(function() {
    const container = document.getElementById('load-more-container');
    if (!container) return;

    const grid = document.getElementById('projects-grid');
    const btn = document.getElementById('load-more-btn');
    const spinner = document.getElementById('load-more-spinner');
    const allLoaded = document.getElementById('all-loaded');

    let currentPage = parseInt(container.dataset.page);
    const totalPages = parseInt(container.dataset.total);
    let loading = false;

    // Get current filters from URL
    const urlParams = new URLSearchParams(window.location.search);
    const filters = {
        sort: urlParams.get('sort') || 'newest',
        category: '<?= isset($currentCategory) ? esc($currentCategory['slug']) : '' ?>',
        ai_tool: '<?= isset($currentTool) ? esc($currentTool['slug']) : '' ?>',
        q: urlParams.get('q') || '',
        tag: '<?= isset($currentTag) ? esc($currentTag['slug']) : '' ?>'
    };

    async function loadMore() {
        if (loading || currentPage >= totalPages) return;

        loading = true;
        btn.classList.add('hidden');
        spinner.classList.remove('hidden');

        const nextPage = currentPage + 1;
        let url = `<?= base_url('api/projects') ?>?page=${nextPage}&sort=${filters.sort}`;
        if (filters.category) url += `&category=${filters.category}`;
        if (filters.ai_tool) url += `&ai_tool=${filters.ai_tool}`;
        if (filters.q) url += `&q=${encodeURIComponent(filters.q)}`;
        if (filters.tag) url += `&tag=${filters.tag}`;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success && data.projects.length > 0) {
                data.projects.forEach(project => {
                    const card = createProjectCard(project);
                    grid.insertAdjacentHTML('beforeend', card);
                });

                currentPage = nextPage;
                container.dataset.page = currentPage;

                if (currentPage >= totalPages) {
                    btn.classList.add('hidden');
                    allLoaded.classList.remove('hidden');
                } else {
                    btn.classList.remove('hidden');
                }
            } else {
                btn.classList.add('hidden');
                allLoaded.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading projects:', error);
            btn.classList.remove('hidden');
        }

        spinner.classList.add('hidden');
        loading = false;
    }

    function createProjectCard(project) {
        const aiToolsHtml = project.ai_tools.map(tool =>
            `<span class="ai-badge">${escapeHtml(tool.name)}</span>`
        ).join('');

        const screenshot = project.screenshot
            ? `<?= base_url() ?>${project.screenshot}`
            : 'https://placehold.co/400x300/1e1b4b/a78bfa?text=No+Image';

        return `
            <article class="project-card group fade-in">
                <a href="<?= base_url('projects') ?>/${project.slug}" class="block">
                    <div class="aspect-video overflow-hidden">
                        <img src="${screenshot}" alt="${escapeHtml(project.title)}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             loading="lazy">
                    </div>
                </a>
                <div class="p-5">
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        ${aiToolsHtml}
                    </div>
                    <a href="<?= base_url('projects') ?>/${project.slug}">
                        <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-purple-300 transition-colors line-clamp-1">
                            ${escapeHtml(project.title)}
                        </h3>
                    </a>
                    <p class="text-slate-400 text-sm mb-4 line-clamp-2">${escapeHtml(project.description)}</p>
                    <div class="flex items-center justify-between text-sm">
                        <a href="<?= base_url('user') ?>/${project.user_id}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors">
                            ${project.user_avatar
                                ? `<img src="${project.user_avatar}" alt="" class="w-6 h-6 rounded-full" referrerpolicy="no-referrer">`
                                : `<div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs">${project.user_name.charAt(0).toUpperCase()}</div>`
                            }
                            <span class="truncate max-w-[100px]">${escapeHtml(project.user_name)}</span>
                        </a>
                        <div class="flex items-center gap-1 text-pink-400">
                            <svg class="w-4 h-4" fill="${project.is_liked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>${project.likes_count}</span>
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Button click handler
    btn.addEventListener('click', loadMore);

    // Infinite scroll (optional - triggers when near bottom)
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const rect = container.getBoundingClientRect();
            if (rect.top < window.innerHeight + 200 && currentPage < totalPages && !loading) {
                loadMore();
            }
        }, 100);
    });
})();
</script>
<?= $this->endSection() ?>
