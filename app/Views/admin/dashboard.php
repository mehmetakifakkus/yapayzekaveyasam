<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Admin Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
            <p class="text-slate-400 mt-1">Genel istatistikler ve hızlı işlemler</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= base_url('admin/projects') ?>" class="btn-secondary">
                Projeler
            </a>
            <a href="<?= base_url('admin/users') ?>" class="btn-secondary">
                Kullanıcılar
            </a>
            <a href="<?= base_url('admin/settings') ?>" class="btn-secondary">
                Ayarlar
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-white"><?= number_format($stats['total_users']) ?></div>
            <div class="text-sm text-slate-400">Toplam Kullanıcı</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-white"><?= number_format($stats['total_projects']) ?></div>
            <div class="text-sm text-slate-400">Toplam Proje</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-yellow-400"><?= number_format($stats['pending_projects']) ?></div>
            <div class="text-sm text-slate-400">Bekleyen Proje</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-green-400"><?= number_format($stats['approved_projects']) ?></div>
            <div class="text-sm text-slate-400">Onaylı Proje</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-red-400"><?= number_format($stats['rejected_projects']) ?></div>
            <div class="text-sm text-slate-400">Reddedilen Proje</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-pink-400"><?= number_format($stats['total_likes']) ?></div>
            <div class="text-sm text-slate-400">Toplam Beğeni</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-blue-400"><?= number_format($stats['total_comments']) ?></div>
            <div class="text-sm text-slate-400">Toplam Yorum</div>
        </div>
        <div class="glass-card p-6">
            <div class="text-3xl font-bold text-orange-400"><?= number_format($stats['banned_users']) ?></div>
            <div class="text-sm text-slate-400">Yasaklı Kullanıcı</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pending Projects -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Onay Bekleyen Projeler</h2>
                <?php if ($stats['pending_projects'] > 5): ?>
                <a href="<?= base_url('admin/projects?status=pending') ?>" class="text-sm text-purple-400 hover:text-purple-300">
                    Tümünü gör (<?= $stats['pending_projects'] ?>)
                </a>
                <?php endif; ?>
            </div>

            <?php if (empty($pendingProjects)): ?>
                <p class="text-slate-500 text-center py-8">Onay bekleyen proje yok.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($pendingProjects as $project): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg" id="pending-project-<?= $project['id'] ?>">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-medium truncate"><?= esc($project['title']) ?></h3>
                            <p class="text-sm text-slate-500">
                                <?= esc($project['user_name']) ?> &bull; <?= esc($project['category_name']) ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <a href="<?= base_url('projects/' . esc($project['slug'])) ?>"
                               target="_blank"
                               class="p-2 text-slate-400 hover:text-white transition-colors"
                               title="Görüntüle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <button onclick="approveProject(<?= $project['id'] ?>)"
                                    class="p-2 text-green-400 hover:text-green-300 transition-colors"
                                    title="Onayla">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                            <button onclick="showRejectModal(<?= $project['id'] ?>)"
                                    class="p-2 text-red-400 hover:text-red-300 transition-colors"
                                    title="Reddet">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Users -->
        <div class="glass-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Son Kullanıcılar</h2>
                <a href="<?= base_url('admin/users') ?>" class="text-sm text-purple-400 hover:text-purple-300">
                    Tümünü gör
                </a>
            </div>

            <?php if (empty($recentUsers)): ?>
                <p class="text-slate-500 text-center py-8">Henüz kullanıcı yok.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentUsers as $user): ?>
                    <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-lg">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= esc($user['avatar']) ?>" alt="" class="w-10 h-10 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-medium truncate"><?= esc($user['name']) ?></h3>
                            <p class="text-sm text-slate-500"><?= esc($user['email']) ?></p>
                        </div>
                        <div class="text-xs text-slate-500">
                            <?= date('d M', strtotime($user['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="glass-card p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-semibold text-white mb-4">Projeyi Reddet</h3>
        <textarea id="reject-reason" rows="3" class="textarea-field mb-4" placeholder="Red sebebi (opsiyonel)..."></textarea>
        <div class="flex justify-end gap-3">
            <button onclick="hideRejectModal()" class="btn-secondary">İptal</button>
            <button onclick="rejectProject()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                Reddet
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let currentRejectId = null;

async function approveProject(id) {
    try {
        const response = await fetch(`<?= base_url('admin/projects') ?>/${id}/approve`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById(`pending-project-${id}`)?.remove();
            showToast('Proje onaylandı', 'success');
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}

function showRejectModal(id) {
    currentRejectId = id;
    document.getElementById('reject-modal').classList.remove('hidden');
    document.getElementById('reject-modal').classList.add('flex');
}

function hideRejectModal() {
    currentRejectId = null;
    document.getElementById('reject-modal').classList.add('hidden');
    document.getElementById('reject-modal').classList.remove('flex');
    document.getElementById('reject-reason').value = '';
}

async function rejectProject() {
    if (!currentRejectId) return;

    const reason = document.getElementById('reject-reason').value;
    const formData = new FormData();
    formData.append('reason', reason);

    try {
        const response = await fetch(`<?= base_url('admin/projects') ?>/${currentRejectId}/reject`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById(`pending-project-${currentRejectId}`)?.remove();
            hideRejectModal();
            showToast('Proje reddedildi', 'success');
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}
</script>
<?= $this->endSection() ?>
