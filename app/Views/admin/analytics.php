<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Admin Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Analytics</h1>
            <p class="text-slate-400 mt-1">Son <?= $days ?> gune ait detayli istatistikler</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= base_url('admin') ?>" class="btn-secondary">Dashboard</a>
            <a href="<?= base_url('admin/projects') ?>" class="btn-secondary">Projeler</a>
            <a href="<?= base_url('admin/users') ?>" class="btn-secondary">Kullanıcılar</a>
            <a href="<?= base_url('admin/settings') ?>" class="btn-secondary">Ayarlar</a>
        </div>
    </div>

    <!-- Activity Chart -->
    <div class="glass-card p-6 mb-8">
        <h2 class="text-xl font-semibold text-white mb-4">Gunluk Aktivite</h2>
        <div class="h-80">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Project Status -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Proje Durumlari</h2>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Projects by Category -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Kategorilere Gore</h2>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Projects by AI Tool -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-white mb-4">AI Araclarina Gore</h2>
            <div class="h-64">
                <canvas id="toolChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Leaderboards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Top Users by Projects -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-white mb-4">En Cok Proje Paylasan</h2>
            <?php if (empty($topUsersByProjects)): ?>
                <p class="text-slate-500 text-center py-8">Henuz veri yok.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($topUsersByProjects as $i => $user): ?>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 text-xs flex items-center justify-center font-bold">
                            <?= $i + 1 ?>
                        </span>
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= esc($user['avatar']) ?>" alt="" class="w-8 h-8 rounded-full object-cover" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <a href="<?= base_url('user/' . $user['id']) ?>" class="text-white text-sm hover:text-purple-400 truncate block">
                                <?= esc($user['name']) ?>
                            </a>
                        </div>
                        <span class="text-emerald-400 text-sm font-medium"><?= $user['projects_count'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Top Users by Likes -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-white mb-4">En Cok Begeni Alan</h2>
            <?php if (empty($topUsersByLikes)): ?>
                <p class="text-slate-500 text-center py-8">Henuz veri yok.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($topUsersByLikes as $i => $user): ?>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-pink-500/20 text-pink-400 text-xs flex items-center justify-center font-bold">
                            <?= $i + 1 ?>
                        </span>
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= esc($user['avatar']) ?>" alt="" class="w-8 h-8 rounded-full object-cover" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <a href="<?= base_url('user/' . $user['id']) ?>" class="text-white text-sm hover:text-purple-400 truncate block">
                                <?= esc($user['name']) ?>
                            </a>
                        </div>
                        <span class="text-pink-400 text-sm font-medium"><?= $user['likes_count'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Top Projects -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-white mb-4">En Populer Projeler</h2>
            <?php if (empty($topProjects)): ?>
                <p class="text-slate-500 text-center py-8">Henuz veri yok.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($topProjects as $i => $project): ?>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 text-xs flex items-center justify-center font-bold">
                            <?= $i + 1 ?>
                        </span>
                        <div class="flex-1 min-w-0">
                            <a href="<?= base_url('projects/' . esc($project['slug'])) ?>" class="text-white text-sm hover:text-purple-400 truncate block">
                                <?= esc($project['title']) ?>
                            </a>
                            <span class="text-slate-500 text-xs"><?= esc($project['user_name']) ?></span>
                        </div>
                        <span class="text-pink-400 text-sm font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <?= $project['likes_count'] ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Prepare data
const dailyUsersData = <?= json_encode($dailyUsers) ?>;
const dailyProjectsData = <?= json_encode($dailyProjects) ?>;
const dailyLikesData = <?= json_encode($dailyLikes) ?>;
const dailyCommentsData = <?= json_encode($dailyComments) ?>;
const projectsByCategory = <?= json_encode($projectsByCategory) ?>;
const projectsByTool = <?= json_encode($projectsByTool) ?>;
const projectStatuses = <?= json_encode($projectStatuses) ?>;

// Generate date labels for the past 30 days
const days = <?= $days ?>;
const dateLabels = [];
for (let i = days - 1; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    dateLabels.push(date.toISOString().split('T')[0]);
}

// Map data to date labels
function mapDataToDates(data) {
    const dataMap = {};
    data.forEach(item => { dataMap[item.date] = parseInt(item.count); });
    return dateLabels.map(date => dataMap[date] || 0);
}

// Chart colors
const colors = {
    purple: 'rgb(168, 85, 247)',
    pink: 'rgb(236, 72, 153)',
    blue: 'rgb(59, 130, 246)',
    emerald: 'rgb(16, 185, 129)',
    amber: 'rgb(245, 158, 11)',
    red: 'rgb(239, 68, 68)',
    slate: 'rgb(100, 116, 139)'
};

// Activity Chart
new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: dateLabels.map(d => {
            const date = new Date(d);
            return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' });
        }),
        datasets: [
            {
                label: 'Kullanicilar',
                data: mapDataToDates(dailyUsersData),
                borderColor: colors.purple,
                backgroundColor: colors.purple + '20',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Projeler',
                data: mapDataToDates(dailyProjectsData),
                borderColor: colors.emerald,
                backgroundColor: colors.emerald + '20',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Begeniler',
                data: mapDataToDates(dailyLikesData),
                borderColor: colors.pink,
                backgroundColor: colors.pink + '20',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Yorumlar',
                data: mapDataToDates(dailyCommentsData),
                borderColor: colors.blue,
                backgroundColor: colors.blue + '20',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                labels: { color: '#94a3b8' }
            }
        },
        scales: {
            x: {
                grid: { color: '#334155' },
                ticks: { color: '#94a3b8' }
            },
            y: {
                beginAtZero: true,
                grid: { color: '#334155' },
                ticks: { color: '#94a3b8' }
            }
        }
    }
});

// Status Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Onayli', 'Bekleyen', 'Reddedilen'],
        datasets: [{
            data: [projectStatuses.approved, projectStatuses.pending, projectStatuses.rejected],
            backgroundColor: [colors.emerald, colors.amber, colors.red],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#94a3b8', padding: 20 }
            }
        }
    }
});

// Category Chart
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: projectsByCategory.map(c => c.name),
        datasets: [{
            label: 'Projeler',
            data: projectsByCategory.map(c => parseInt(c.count)),
            backgroundColor: colors.purple,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: '#334155' },
                ticks: { color: '#94a3b8' }
            },
            y: {
                grid: { display: false },
                ticks: { color: '#94a3b8' }
            }
        }
    }
});

// Tool Chart
new Chart(document.getElementById('toolChart'), {
    type: 'bar',
    data: {
        labels: projectsByTool.map(t => t.name),
        datasets: [{
            label: 'Projeler',
            data: projectsByTool.map(t => parseInt(t.count)),
            backgroundColor: colors.emerald,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: '#334155' },
                ticks: { color: '#94a3b8' }
            },
            y: {
                grid: { display: false },
                ticks: { color: '#94a3b8' }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
