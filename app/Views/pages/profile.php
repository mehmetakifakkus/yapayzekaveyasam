<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Profile Header -->
    <div class="glass-card p-6 sm:p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Avatar -->
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= esc($user['avatar']) ?>" alt="<?= esc($user['name']) ?>" class="w-24 h-24 rounded-full object-cover" referrerpolicy="no-referrer">
            <?php else: ?>
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white text-3xl font-bold">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <!-- Info -->
            <div class="flex-1 text-center sm:text-left">
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2"><?= esc($user['name']) ?></h1>

                <!-- Bio -->
                <?php if ($isOwnProfile): ?>
                    <div id="bio-display" class="mb-4">
                        <p id="bio-text" class="text-slate-400 <?= empty($user['bio']) ? 'italic' : '' ?>">
                            <?= !empty($user['bio']) ? esc($user['bio']) : 'Henüz bio eklenmemiş' ?>
                        </p>
                        <button onclick="showBioEdit()" class="text-sm text-purple-400 hover:text-purple-300 mt-2">
                            <?= !empty($user['bio']) ? 'Düzenle' : 'Bio Ekle' ?>
                        </button>
                    </div>
                    <div id="bio-edit" class="hidden mb-4">
                        <textarea
                            id="bio-input"
                            rows="3"
                            maxlength="500"
                            placeholder="Kendiniz hakkında birkaç cümle yazın..."
                            class="textarea-field mb-2"
                        ><?= esc($user['bio'] ?? '') ?></textarea>
                        <div class="flex items-center gap-2">
                            <button onclick="saveBio()" class="btn-primary text-sm py-2">Kaydet</button>
                            <button onclick="hideBioEdit()" class="btn-secondary text-sm py-2">İptal</button>
                            <span id="bio-char-count" class="text-xs text-slate-500 ml-auto">0/500</span>
                        </div>
                    </div>
                <?php elseif (!empty($user['bio'])): ?>
                    <p class="text-slate-400 mb-4"><?= esc($user['bio']) ?></p>
                <?php endif; ?>

                <!-- Badges -->
                <?php if (!empty($badges)): ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($badges as $badge): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 text-sm" title="<?= esc($badge['description']) ?>">
                            <span><?= $badge['icon'] ?></span>
                            <span class="text-purple-200"><?= esc($badge['name']) ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Stats -->
                <div class="flex items-center justify-center sm:justify-start gap-6">
                    <div class="text-center sm:text-left">
                        <div class="text-2xl font-bold text-white"><?= number_format($projectsCount) ?></div>
                        <div class="text-sm text-slate-500">Proje</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-2xl font-bold text-white"><?= number_format($likesReceived) ?></div>
                        <div class="text-sm text-slate-500">Beğeni</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-2xl font-bold text-white" id="follower-count"><?= number_format($followerCount) ?></div>
                        <div class="text-sm text-slate-500">Takipçi</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-2xl font-bold text-white"><?= number_format($followingCount) ?></div>
                        <div class="text-sm text-slate-500">Takip</div>
                    </div>
                </div>

                <!-- Member since -->
                <div class="text-sm text-slate-500 mt-4">
                    Üye: <?= date('M Y', strtotime($user['created_at'])) ?>
                </div>

                <!-- Email Digest Toggle (only for own profile) -->
                <?php if ($isOwnProfile): ?>
                <div class="mt-4 pt-4 border-t border-slate-700">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" id="digest-toggle" class="sr-only peer" <?= !empty($user['email_digest']) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </div>
                        <span class="text-sm text-slate-400">Haftalık özet e-postası al</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="flex-shrink-0 flex flex-col gap-2">
                <?php if ($isOwnProfile): ?>
                    <a href="<?= base_url('projects/create') ?>" class="btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Yeni Proje
                    </a>
                    <a href="<?= base_url('user/' . $user['id'] . '/bookmarks') ?>" class="btn-secondary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        Kaydedilenler
                    </a>
                <?php elseif (isset($isLoggedIn) && $isLoggedIn): ?>
                    <button
                        onclick="toggleFollow(<?= $user['id'] ?>, this)"
                        class="<?= $isFollowing ? 'btn-secondary' : 'btn-primary' ?> flex items-center gap-2"
                    >
                        <?php if ($isFollowing): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Takip Ediliyor
                        <?php else: ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Takip Et
                        <?php endif; ?>
                    </button>
                    <a href="<?= base_url('messages/new/' . $user['id']) ?>" class="btn-secondary flex items-center gap-2" title="<?= $canMessage ?? false ? 'Mesaj Gönder' : 'Mesaj göndermek için takip edilmeniz gerekiyor' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Mesaj
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Projects Section -->
    <div>
        <h2 class="text-xl font-bold text-white mb-6">
            <?= $isOwnProfile ? 'Projelerim' : 'Projeler' ?>
            <span class="text-slate-500 font-normal">(<?= count($projects) ?>)</span>
        </h2>

        <?php if (empty($projects)): ?>
            <div class="glass-card p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-xl font-semibold text-white mb-2">Henüz Proje Yok</h3>
                <?php if ($isOwnProfile): ?>
                    <p class="text-slate-400 mb-6">AI araçlarıyla yaptığınız ilk projenizi paylaşın!</p>
                    <a href="<?= base_url('projects/create') ?>" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        İlk Projeyi Ekle
                    </a>
                <?php else: ?>
                    <p class="text-slate-400">Bu kullanıcı henüz proje paylaşmamış.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card group relative">
                        <?php if ($isOwnProfile): ?>
                        <!-- Edit/Delete buttons for own projects -->
                        <div class="absolute top-3 right-3 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="<?= base_url('projects/' . esc($project['slug']) . '/edit') ?>"
                               class="p-2 rounded-lg bg-slate-900/80 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"
                               title="Düzenle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                        <?php endif; ?>

                        <!-- Status Badge -->
                        <?php if ($project['status'] !== 'approved'): ?>
                        <div class="absolute top-3 left-3 z-10">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $project['status'] === 'pending' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-red-500/20 text-red-300' ?>">
                                <?= $project['status'] === 'pending' ? 'Beklemede' : 'Reddedildi' ?>
                            </span>
                        </div>
                        <?php endif; ?>

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
                                        <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <span class="category-badge text-xs mb-2 inline-block"><?= esc($project['category_name']) ?></span>
                                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-purple-300 transition-colors line-clamp-1">
                                    <?= esc($project['title']) ?>
                                </h3>

                                <!-- AI Tools -->
                                <?php if (!empty($project['ai_tools'])): ?>
                                <div class="flex flex-wrap gap-1 mb-3">
                                    <?php foreach (array_slice($project['ai_tools'], 0, 2) as $tool): ?>
                                        <span class="ai-badge text-xs py-1"><?= esc($tool['name']) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($project['ai_tools']) > 2): ?>
                                        <span class="ai-badge text-xs py-1">+<?= count($project['ai_tools']) - 2 ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <!-- Stats -->
                                <div class="flex items-center gap-4 text-sm text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <?= number_format($project['likes_count'] ?? 0) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <?= number_format($project['views'] ?? 0) ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?php if ($isOwnProfile): ?>
<?= $this->section('scripts') ?>
<script>
const bioInput = document.getElementById('bio-input');
const bioCharCount = document.getElementById('bio-char-count');

if (bioInput) {
    bioInput.addEventListener('input', () => {
        bioCharCount.textContent = `${bioInput.value.length}/500`;
    });
    // Initial count
    bioCharCount.textContent = `${bioInput.value.length}/500`;
}

function showBioEdit() {
    document.getElementById('bio-display').classList.add('hidden');
    document.getElementById('bio-edit').classList.remove('hidden');
    bioInput.focus();
}

function hideBioEdit() {
    document.getElementById('bio-display').classList.remove('hidden');
    document.getElementById('bio-edit').classList.add('hidden');
}

async function saveBio() {
    const bio = bioInput.value.trim();

    try {
        const formData = new FormData();
        formData.append('bio', bio);

        const response = await fetch('<?= base_url('user/update-bio') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const bioText = document.getElementById('bio-text');
            if (bio) {
                bioText.textContent = bio;
                bioText.classList.remove('italic');
            } else {
                bioText.textContent = 'Henüz bio eklenmemiş';
                bioText.classList.add('italic');
            }
            hideBioEdit();
        } else {
            alert(data.message || 'Bir hata oluştu');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Email digest toggle
const digestToggle = document.getElementById('digest-toggle');
if (digestToggle) {
    digestToggle.addEventListener('change', async function() {
        try {
            const formData = new FormData();
            formData.append('enabled', this.checked ? '1' : '0');

            const response = await fetch('<?= base_url('user/update-digest') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success && window.showToast) {
                showToast(data.message, 'success');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}
</script>
<?= $this->endSection() ?>
<?php endif; ?>
