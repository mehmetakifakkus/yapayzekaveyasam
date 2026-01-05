<article class="project-card group">
    <a href="<?= base_url('projects/' . esc($project['slug'])) ?>" class="block">
        <!-- Screenshot -->
        <div class="screenshot-container aspect-video bg-slate-800">
            <?php if (!empty($project['screenshot'])): ?>
                <img
                    src="<?= base_url($project['screenshot']) ?>"
                    alt="<?= esc($project['title']) ?>"
                    class="w-full h-full object-cover"
                    loading="lazy"
                >
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-700">
                    <svg class="w-16 h-16 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            <?php endif; ?>

            <!-- Overlay gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- View button on hover -->
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <span class="btn-primary text-sm py-2 px-4">
                    Projeyi Görüntüle
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5">
            <!-- Category -->
            <div class="mb-3">
                <span class="category-badge">
                    <?= esc($project['category_name']) ?>
                </span>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-purple-300 transition-colors line-clamp-1">
                <?= esc($project['title']) ?>
            </h3>

            <!-- Description -->
            <p class="text-slate-400 text-sm mb-4 line-clamp-2">
                <?= esc(character_limiter($project['description'], 100)) ?>
            </p>

            <!-- AI Tools -->
            <?php if (!empty($project['ai_tools'])): ?>
            <div class="flex flex-wrap gap-2 mb-4">
                <?php foreach (array_slice($project['ai_tools'], 0, 3) as $tool): ?>
                    <span class="ai-badge">
                        <?= esc($tool['name']) ?>
                    </span>
                <?php endforeach; ?>
                <?php if (count($project['ai_tools']) > 3): ?>
                    <span class="ai-badge">+<?= count($project['ai_tools']) - 3 ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-700/50">
                <!-- User -->
                <div class="flex items-center gap-2">
                    <?php if (!empty($project['user_avatar'])): ?>
                        <img src="<?= esc($project['user_avatar']) ?>" alt="" class="w-6 h-6 rounded-full">
                    <?php else: ?>
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-medium">
                            <?= strtoupper(substr($project['user_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <span class="text-sm text-slate-400 truncate max-w-[100px]"><?= esc($project['user_name'] ?? 'Anonim') ?></span>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-4">
                    <span class="stats-badge">
                        <svg class="w-4 h-4 <?= $project['is_liked'] ? 'text-pink-500 fill-current' : '' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <?= number_format($project['likes_count'] ?? 0) ?>
                    </span>
                    <span class="stats-badge">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <?= number_format($project['views'] ?? 0) ?>
                    </span>
                </div>
            </div>
        </div>
    </a>
</article>
