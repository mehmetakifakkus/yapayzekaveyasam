<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm">
            <li><a href="<?= base_url('/') ?>" class="text-slate-400 hover:text-white transition-colors">Ana Sayfa</a></li>
            <li class="text-slate-600">/</li>
            <li><a href="<?= base_url('projects') ?>" class="text-slate-400 hover:text-white transition-colors">Projeler</a></li>
            <li class="text-slate-600">/</li>
            <li class="text-purple-400"><?= esc($project['title']) ?></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Screenshot -->
            <div class="glass-card overflow-hidden">
                <?php if (!empty($project['screenshot'])): ?>
                    <img
                        src="<?= base_url($project['screenshot']) ?>"
                        alt="<?= esc($project['title']) ?>"
                        class="w-full aspect-video object-cover"
                    >
                <?php else: ?>
                    <div class="w-full aspect-video bg-gradient-to-br from-slate-800 to-slate-700 flex items-center justify-center">
                        <svg class="w-24 h-24 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Project Info -->
            <div class="glass-card p-6">
                <!-- Category & AI Tools -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <a href="<?= base_url('category/' . esc($project['category_slug'])) ?>" class="category-badge">
                        <?= esc($project['category_name']) ?>
                    </a>
                    <?php foreach ($project['ai_tools'] as $tool): ?>
                        <a href="<?= base_url('tool/' . esc($tool['slug'])) ?>" class="ai-badge">
                            <?= esc($tool['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-white mb-4"><?= esc($project['title']) ?></h1>

                <!-- Description -->
                <div class="prose prose-invert max-w-none">
                    <p class="text-slate-300 leading-relaxed whitespace-pre-line"><?= esc($project['description']) ?></p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-slate-700">
                    <a href="<?= esc($project['website_url']) ?>" target="_blank" rel="noopener" class="btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Projeyi Ziyaret Et
                    </a>

                    <?php if (!empty($project['github_url'])): ?>
                    <a href="<?= esc($project['github_url']) ?>" target="_blank" rel="noopener" class="btn-secondary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        GitHub
                    </a>
                    <?php endif; ?>

                    <!-- Like Button -->
                    <button
                        onclick="toggleLike(<?= $project['id'] ?>)"
                        id="like-btn"
                        class="like-btn <?= $project['is_liked'] ? 'liked' : '' ?>"
                    >
                        <svg class="w-5 h-5" fill="<?= $project['is_liked'] ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span id="like-count"><?= number_format($project['likes_count']) ?></span>
                    </button>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Yorumlar (<span id="comments-count"><?= count($comments) ?></span>)
                </h2>

                <!-- Comment Form -->
                <?php if ($isLoggedIn): ?>
                <form id="comment-form" class="mb-6">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                    <textarea
                        name="content"
                        id="comment-content"
                        rows="3"
                        placeholder="Yorum yazın..."
                        class="textarea-field mb-3"
                        maxlength="1000"
                    ></textarea>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500"><span id="char-count">0</span>/1000</span>
                        <button type="submit" class="btn-primary text-sm py-2">
                            Yorum Yap
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="mb-6 p-4 bg-slate-800/50 rounded-xl text-center">
                    <p class="text-slate-400 mb-3">Yorum yapmak için giriş yapın</p>
                    <a href="<?= base_url('auth/google') ?>" class="btn-primary text-sm py-2 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        </svg>
                        Google ile Giriş Yap
                    </a>
                </div>
                <?php endif; ?>

                <!-- Comments List -->
                <div id="comments-list" class="space-y-4">
                    <?php if (empty($comments)): ?>
                        <p id="no-comments" class="text-slate-500 text-center py-4">Henüz yorum yok. İlk yorumu siz yapın!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                        <div class="comment-box" data-comment-id="<?= $comment['id'] ?>">
                            <div class="flex items-start gap-3">
                                <?php if (!empty($comment['user_avatar'])): ?>
                                    <img src="<?= esc($comment['user_avatar']) ?>" alt="" class="w-8 h-8 rounded-full" referrerpolicy="no-referrer">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-medium">
                                        <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-white text-sm"><?= esc($comment['user_name']) ?></span>
                                        <span class="text-xs text-slate-500"><?= date('d M Y, H:i', strtotime($comment['created_at'])) ?></span>
                                    </div>
                                    <p class="text-slate-300 text-sm"><?= nl2br(esc($comment['content'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Author Card -->
            <div class="glass-card p-6">
                <h3 class="text-sm text-slate-400 mb-4">Geliştirici</h3>
                <a href="<?= base_url('user/' . $project['user_id']) ?>" class="flex items-center gap-3 group">
                    <?php if (!empty($project['user_avatar'])): ?>
                        <img src="<?= esc($project['user_avatar']) ?>" alt="" class="w-12 h-12 rounded-full" referrerpolicy="no-referrer">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium">
                            <?= strtoupper(substr($project['user_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <p class="font-medium text-white group-hover:text-purple-300 transition-colors"><?= esc($project['user_name']) ?></p>
                        <p class="text-xs text-slate-400">Profili Görüntüle</p>
                    </div>
                </a>
            </div>

            <!-- Stats Card -->
            <div class="glass-card p-6">
                <h3 class="text-sm text-slate-400 mb-4">İstatistikler</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Görüntülenme
                        </span>
                        <span class="text-white font-medium"><?= number_format($project['views']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Beğeni
                        </span>
                        <span class="text-white font-medium"><?= number_format($project['likes_count']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Yorum
                        </span>
                        <span class="text-white font-medium"><?= number_format($project['comments_count']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Eklenme Tarihi
                        </span>
                        <span class="text-white font-medium"><?= date('d M Y', strtotime($project['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Edit/Delete (if owner) -->
            <?php if ($isLoggedIn && $currentUser['id'] == $project['user_id']): ?>
            <div class="glass-card p-6">
                <h3 class="text-sm text-slate-400 mb-4">Yönetim</h3>
                <div class="space-y-2">
                    <a href="<?= base_url('projects/' . $project['slug'] . '/edit') ?>" class="w-full btn-secondary text-center block">
                        Düzenle
                    </a>
                    <button onclick="deleteProject('<?= esc($project['slug']) ?>')" class="w-full btn-secondary text-red-400 hover:text-red-300 hover:border-red-500/50">
                        Sil
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Related Projects -->
            <?php if (!empty($relatedProjects)): ?>
            <div class="glass-card p-6">
                <h3 class="text-sm text-slate-400 mb-4">Benzer Projeler</h3>
                <div class="space-y-4">
                    <?php foreach ($relatedProjects as $related): ?>
                    <a href="<?= base_url('projects/' . esc($related['slug'])) ?>" class="flex items-center gap-3 group">
                        <div class="w-16 h-12 rounded-lg overflow-hidden bg-slate-800 flex-shrink-0">
                            <?php if (!empty($related['screenshot'])): ?>
                                <img src="<?= base_url($related['screenshot']) ?>" alt="" class="w-full h-full object-cover">
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate group-hover:text-purple-300 transition-colors"><?= esc($related['title']) ?></p>
                            <p class="text-xs text-slate-500"><?= number_format($related['likes_count']) ?> beğeni</p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const projectId = <?= $project['id'] ?>;
const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

// Like toggle
async function toggleLike(id) {
    if (!isLoggedIn) {
        window.location.href = '<?= base_url('auth/google') ?>';
        return;
    }

    try {
        const response = await fetch(`<?= base_url('api/like') ?>/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            const btn = document.getElementById('like-btn');
            const count = document.getElementById('like-count');

            if (data.action === 'liked') {
                btn.classList.add('liked');
                btn.querySelector('svg').setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('liked');
                btn.querySelector('svg').setAttribute('fill', 'none');
            }

            count.textContent = data.count;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Comment form
const commentForm = document.getElementById('comment-form');
const commentContent = document.getElementById('comment-content');
const charCount = document.getElementById('char-count');

if (commentContent) {
    commentContent.addEventListener('input', () => {
        charCount.textContent = commentContent.value.length;
    });
}

if (commentForm) {
    commentForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const content = commentContent.value.trim();
        if (!content) return;

        try {
            const formData = new FormData();
            formData.append('project_id', projectId);
            formData.append('content', content);

            const response = await fetch('<?= base_url('api/comment') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Remove "no comments" message
                const noComments = document.getElementById('no-comments');
                if (noComments) noComments.remove();

                // Add new comment
                const commentsList = document.getElementById('comments-list');
                const avatar = data.comment.user_avatar
                    ? `<img src="${data.comment.user_avatar}" alt="" class="w-8 h-8 rounded-full">`
                    : `<div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-medium">${data.comment.user_name.charAt(0).toUpperCase()}</div>`;

                const newComment = document.createElement('div');
                newComment.className = 'comment-box fade-in';
                newComment.dataset.commentId = data.comment.id;
                newComment.innerHTML = `
                    <div class="flex items-start gap-3">
                        ${avatar}
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-white text-sm">${data.comment.user_name}</span>
                                <span class="text-xs text-slate-500">${data.comment.formatted_date}</span>
                            </div>
                            <p class="text-slate-300 text-sm">${data.comment.content.replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>
                `;

                commentsList.insertBefore(newComment, commentsList.firstChild);

                // Update count
                document.getElementById('comments-count').textContent = data.count;

                // Clear form
                commentContent.value = '';
                charCount.textContent = '0';
            } else {
                alert(data.message || 'Bir hata oluştu');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}

// Delete project
async function deleteProject(slug) {
    if (!confirm('Bu projeyi silmek istediğinize emin misiniz?')) return;

    try {
        const response = await fetch(`<?= base_url('projects') ?>/${slug}/delete`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = '<?= base_url('/') ?>';
        } else {
            alert(data.message || 'Silme işlemi başarısız');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>
<?= $this->endSection() ?>
