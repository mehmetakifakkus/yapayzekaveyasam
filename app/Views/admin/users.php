<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Kullanıcı Yönetimi</h1>
            <p class="text-slate-400 mt-1">Toplam <?= number_format($totalUsers) ?> kullanıcı</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= base_url('admin') ?>" class="btn-secondary">Dashboard</a>
            <a href="<?= base_url('admin/analytics') ?>" class="btn-secondary">Analytics</a>
            <a href="<?= base_url('admin/projects') ?>" class="btn-secondary">Projeler</a>
            <a href="<?= base_url('admin/settings') ?>" class="btn-secondary">Ayarlar</a>
        </div>
    </div>

    <!-- Users Table -->
    <?php if (empty($users)): ?>
        <div class="glass-card p-12 text-center">
            <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-slate-400">Henüz kullanıcı yok.</p>
        </div>
    <?php else: ?>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Kullanıcı</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Email</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Projeler</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Durum</th>
                            <th class="text-left text-xs font-medium text-slate-400 uppercase px-4 py-3">Kayıt Tarihi</th>
                            <th class="text-right text-xs font-medium text-slate-400 uppercase px-4 py-3">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-800/30" id="user-row-<?= $user['id'] ?>">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?= esc($user['avatar']) ?>" alt="" class="w-10 h-10 rounded-full object-cover" referrerpolicy="no-referrer">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?= base_url('user/' . $user['id']) ?>" target="_blank" class="text-white font-medium hover:text-purple-400 transition-colors">
                                            <?= esc($user['name']) ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-slate-400"><?= esc($user['email']) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-white"><?= $user['approved_projects'] ?></span>
                                    <span class="text-slate-500">/</span>
                                    <span class="text-slate-400"><?= $user['projects_count'] ?></span>
                                    <span class="text-xs text-slate-500">(onaylı/toplam)</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span id="user-status-<?= $user['id'] ?>" class="px-2 py-1 text-xs font-medium rounded-full <?= $user['is_banned'] ? 'bg-red-500/20 text-red-300' : 'bg-green-500/20 text-green-300' ?>">
                                    <?= $user['is_banned'] ? 'Yasaklı' : 'Aktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-slate-500"><?= date('d M Y', strtotime($user['created_at'])) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= base_url('user/' . $user['id']) ?>"
                                       target="_blank"
                                       class="p-2 text-slate-400 hover:text-white transition-colors"
                                       title="Profili Görüntüle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <?php if ($user['is_banned']): ?>
                                    <button onclick="unbanUser(<?= $user['id'] ?>)"
                                            class="p-2 text-green-400 hover:text-green-300 transition-colors"
                                            title="Yasağı Kaldır">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <?php else: ?>
                                    <button onclick="banUser(<?= $user['id'] ?>)"
                                            class="p-2 text-red-400 hover:text-red-300 transition-colors"
                                            title="Yasakla">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
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
                    <a href="<?= base_url('admin/users?page=' . $i) ?>"
                       class="px-4 py-2 rounded-lg text-sm <?= $i === $currentPage ? 'bg-purple-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
async function banUser(id) {
    if (!confirm('Bu kullanıcıyı yasaklamak istediğinizden emin misiniz?')) return;

    try {
        const response = await fetch(`<?= base_url('admin/users') ?>/${id}/ban`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}

async function unbanUser(id) {
    try {
        const response = await fetch(`<?= base_url('admin/users') ?>/${id}/unban`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}
</script>
<?= $this->endSection() ?>
