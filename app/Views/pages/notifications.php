<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-white">Bildirimler</h1>
        <?php if (!empty($notifications)): ?>
        <form action="<?= base_url('notifications/mark-all-read') ?>" method="post">
            <button type="submit" class="text-sm text-purple-400 hover:text-purple-300 transition-colors">
                Tümünü Okundu İşaretle
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="glass-card p-12 text-center">
            <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="text-xl font-semibold text-white mb-2">Bildirim Yok</h3>
            <p class="text-slate-400">Henüz bildiriminiz bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div class="glass-card divide-y divide-slate-700/50">
            <?php foreach ($notifications as $notification): ?>
                <?php
                    $icon = '';
                    $iconColor = '';
                    $message = '';

                    switch ($notification['type']) {
                        case 'like':
                            $icon = '<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>';
                            $iconColor = 'text-pink-500';
                            $message = 'projenizi beğendi';
                            break;
                        case 'comment':
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>';
                            $iconColor = 'text-blue-500';
                            $message = 'projenize yorum yaptı';
                            break;
                        case 'follow':
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>';
                            $iconColor = 'text-purple-500';
                            $message = 'sizi takip etmeye başladı';
                            break;
                        case 'approve':
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                            $iconColor = 'text-green-500';
                            $message = 'projeniz onaylandı';
                            break;
                    }

                    $url = $notification['project_slug']
                        ? base_url('projects/' . $notification['project_slug'])
                        : base_url('user/' . $notification['actor_id']);
                ?>
                <a href="<?= $url ?>" class="flex items-start gap-4 p-4 hover:bg-slate-700/30 transition-colors <?= !$notification['is_read'] ? 'bg-purple-500/5' : '' ?>">
                    <!-- Actor Avatar -->
                    <?php if (!empty($notification['actor_avatar'])): ?>
                        <img src="<?= esc($notification['actor_avatar']) ?>" alt="" class="w-10 h-10 rounded-full flex-shrink-0">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                            <?= strtoupper(substr($notification['actor_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-slate-300">
                            <span class="font-medium text-white"><?= esc($notification['actor_name']) ?></span>
                            <?php if ($notification['project_title'] && $notification['type'] !== 'follow'): ?>
                                "<?= esc($notification['project_title']) ?>" adlı <?= $message ?>
                            <?php else: ?>
                                <?= $message ?>
                            <?php endif; ?>
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            <?= date('d M Y, H:i', strtotime($notification['created_at'])) ?>
                        </p>
                    </div>

                    <!-- Icon -->
                    <svg class="w-5 h-5 <?= $iconColor ?> flex-shrink-0" fill="<?= $notification['type'] === 'like' ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                        <?= $icon ?>
                    </svg>

                    <!-- Unread indicator -->
                    <?php if (!$notification['is_read']): ?>
                        <div class="w-2 h-2 rounded-full bg-purple-500 flex-shrink-0"></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
