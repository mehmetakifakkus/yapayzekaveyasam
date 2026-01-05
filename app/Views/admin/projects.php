<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Proje Yönetimi</h1>
            <p class="text-slate-400 mt-1">Toplam <?= number_format($totalProjects) ?> proje</p>
        </div>
        <a href="<?= base_url('admin') ?>" class="btn-secondary">
            Dashboard
        </a>
    </div>

    <!-- Status Filter -->
    <div class="flex gap-2 mb-6">
        <a href="<?= base_url('admin/projects') ?>"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentStatus === 'all' ? 'bg-purple-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' ?>">
            Tümü
        </a>
        <a href="<?= base_url('admin/projects?status=pending') ?>"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentStatus === 'pending' ? 'bg-yellow-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' ?>">
            Bekleyen
        </a>
        <a href="<?= base_url('admin/projects?status=approved') ?>"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' ?>">
            Onaylı
        </a>
        <a href="<?= base_url('admin/projects?status=rejected') ?>"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $currentStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' ?>">
            Reddedilen
        </a>
    </div>

    <!-- Projects Table -->
    <?php if (empty($projects)): ?>
        <div class="glass-card p-12 text-center">
            <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p class="text-slate-400">Bu filtrede proje bulunamadı.</p>
        </div>
    <?php else: ?>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Proje</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Kullanıcı</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Kategori</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Durum</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Tarih</th>
                            <th class="text-right text-xs font-medium text-slate-400 uppercase px-4 py-3">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php foreach ($projects as $project): ?>
                        <tr class="hover:bg-slate-800/30" id="project-row-<?= $project['id'] ?>">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($project['screenshot'])): ?>
                                        <img src="<?= base_url($project['screenshot']) ?>" alt="" class="w-16 h-10 rounded object-cover">
                                    <?php else: ?>
                                        <div class="w-16 h-10 rounded bg-slate-700 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?= base_url('projects/' . esc($project['slug'])) ?>" target="_blank" class="text-white font-medium hover:text-purple-400 transition-colors">
                                            <?= esc($project['title']) ?>
                                        </a>
                                        <div class="flex gap-1 mt-1">
                                            <?php foreach (array_slice($project['ai_tools'], 0, 2) as $tool): ?>
                                                <span class="text-xs bg-slate-700 text-slate-300 px-1.5 py-0.5 rounded"><?= esc($tool['name']) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <div class="text-white text-sm"><?= esc($project['user_name']) ?></div>
                                    <div class="text-slate-500 text-xs"><?= esc($project['user_email']) ?></div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-slate-400"><?= esc($project['category_name']) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <span id="status-badge-<?= $project['id'] ?>" class="px-2 py-1 text-xs font-medium rounded-full <?php
                                    echo match($project['status']) {
                                        'approved' => 'bg-green-500/20 text-green-300',
                                        'pending' => 'bg-yellow-500/20 text-yellow-300',
                                        'rejected' => 'bg-red-500/20 text-red-300',
                                        default => 'bg-slate-500/20 text-slate-300'
                                    };
                                ?>">
                                    <?= match($project['status']) {
                                        'approved' => 'Onaylı',
                                        'pending' => 'Bekliyor',
                                        'rejected' => 'Reddedildi',
                                        default => $project['status']
                                    } ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-slate-500"><?= date('d M Y', strtotime($project['created_at'])) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= base_url('projects/' . esc($project['slug'])) ?>"
                                       target="_blank"
                                       class="p-2 text-slate-400 hover:text-white transition-colors"
                                       title="Görüntüle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <?php if ($project['status'] !== 'approved'): ?>
                                    <button onclick="approveProject(<?= $project['id'] ?>)"
                                            class="p-2 text-green-400 hover:text-green-300 transition-colors"
                                            title="Onayla">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($project['status'] !== 'rejected'): ?>
                                    <button onclick="showRejectModal(<?= $project['id'] ?>)"
                                            class="p-2 text-yellow-400 hover:text-yellow-300 transition-colors"
                                            title="Reddet">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                    <button onclick="deleteProject(<?= $project['id'] ?>)"
                                            class="p-2 text-red-400 hover:text-red-300 transition-colors"
                                            title="Sil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-6">
            <div class="flex gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= base_url('admin/projects?status=' . esc($currentStatus) . '&page=' . $i) ?>"
                       class="px-4 py-2 rounded-lg text-sm <?= $i === $currentPage ? 'bg-purple-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
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
            const badge = document.getElementById(`status-badge-${id}`);
            if (badge) {
                badge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300';
                badge.textContent = 'Onaylı';
            }
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
            const badge = document.getElementById(`status-badge-${currentRejectId}`);
            if (badge) {
                badge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-300';
                badge.textContent = 'Reddedildi';
            }
            hideRejectModal();
            showToast('Proje reddedildi', 'success');
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}

async function deleteProject(id) {
    if (!confirm('Bu projeyi silmek istediğinizden emin misiniz?')) return;

    try {
        const response = await fetch(`<?= base_url('admin/projects') ?>/${id}`, {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById(`project-row-${id}`)?.remove();
            showToast('Proje silindi', 'success');
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}
</script>
<?= $this->endSection() ?>
