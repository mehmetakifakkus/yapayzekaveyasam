<nav class="sticky top-0 z-50 backdrop-blur-xl bg-slate-900/80 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold gradient-text hidden sm:block">AI Showcase</span>
            </a>

            <!-- Search (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <form action="<?= base_url('projects') ?>" method="get" class="w-full relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="Proje ara..."
                        value="<?= esc($filters['search'] ?? '') ?>"
                        class="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center gap-4">
                <a href="<?= base_url('projects') ?>" class="nav-link hidden sm:block">Projeler</a>

                <!-- Theme Toggle -->
                <button onclick="toggleDarkMode()" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-colors" title="Tema Değiştir" id="theme-toggle-btn">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg id="theme-icon-sun" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg id="theme-icon-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <?php if ($isLoggedIn): ?>
                    <!-- Add Project Button -->
                    <a href="<?= base_url('projects/create') ?>" class="btn-primary text-sm py-2 hidden sm:flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Proje Ekle</span>
                    </a>

                    <!-- Feed Link -->
                    <a href="<?= base_url('feed') ?>" class="hidden sm:flex p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-colors" title="Takip Akışı">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </a>

                    <!-- Messages -->
                    <a href="<?= base_url('messages') ?>" class="relative p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-colors" title="Mesajlar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span id="message-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-purple-500 text-white text-xs font-medium rounded-full flex items-center justify-center px-1">0</span>
                    </a>

                    <!-- Notifications -->
                    <div class="relative" id="notification-dropdown">
                        <button
                            onclick="toggleNotifications()"
                            class="relative p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"
                            title="Bildirimler"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span id="notification-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-medium rounded-full flex items-center justify-center px-1">0</span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notification-menu" class="hidden absolute right-0 mt-2 w-80 glass-card shadow-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-700">
                                <h3 class="font-medium text-white">Bildirimler</h3>
                                <a href="<?= base_url('notifications') ?>" class="text-xs text-purple-400 hover:text-purple-300">Tümünü Gör</a>
                            </div>
                            <div id="notification-list" class="max-h-80 overflow-y-auto">
                                <div class="px-4 py-8 text-center text-slate-400 text-sm">
                                    Yükleniyor...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button
                            onclick="this.nextElementSibling.classList.toggle('hidden')"
                            class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-800 transition-colors"
                        >
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?= esc($currentUser['avatar']) ?>" alt="<?= esc($currentUser['name']) ?>" class="w-8 h-8 rounded-full object-cover" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium">
                                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="hidden absolute right-0 mt-2 w-48 glass-card p-2 shadow-xl">
                            <div class="px-3 py-2 border-b border-slate-700 mb-2">
                                <p class="text-sm font-medium text-white truncate"><?= esc($currentUser['name']) ?></p>
                                <p class="text-xs text-slate-400 truncate"><?= esc($currentUser['email']) ?></p>
                            </div>

                            <a href="<?= base_url('user/' . $currentUser['id']) ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profilim
                            </a>

                            <a href="<?= base_url('user/' . $currentUser['id'] . '/bookmarks') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                Kaydedilenler
                            </a>

                            <a href="<?= base_url('feed') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors sm:hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                                Takip Akışı
                            </a>

                            <a href="<?= base_url('notifications') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors sm:hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                Bildirimler
                            </a>

                            <a href="<?= base_url('messages') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors sm:hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Mesajlar
                            </a>

                            <a href="<?= base_url('projects/create') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors sm:hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Proje Ekle
                            </a>

                            <!-- Theme Selector -->
                            <div class="px-3 py-2">
                                <label class="text-xs text-slate-500 mb-2 block">Tema</label>
                                <select onchange="changeTheme(this.value)" class="w-full text-sm bg-slate-800 border border-slate-700 rounded-lg px-2 py-1.5 text-slate-300 focus:outline-none focus:border-purple-500">
                                    <option value="default" <?= ($currentUser['theme'] ?? 'default') === 'default' ? 'selected' : '' ?>>Varsayılan</option>
                                    <option value="emerald" <?= ($currentUser['theme'] ?? '') === 'emerald' ? 'selected' : '' ?>>Zümrüt</option>
                                    <option value="amber" <?= ($currentUser['theme'] ?? '') === 'amber' ? 'selected' : '' ?>>Amber</option>
                                    <option value="ocean" <?= ($currentUser['theme'] ?? '') === 'ocean' ? 'selected' : '' ?>>Okyanus</option>
                                    <option value="mono" <?= ($currentUser['theme'] ?? '') === 'mono' ? 'selected' : '' ?>>Mono</option>
                                    <option value="light-white" <?= ($currentUser['theme'] ?? '') === 'light-white' ? 'selected' : '' ?>>Açık Beyaz</option>
                                    <option value="light-cream" <?= ($currentUser['theme'] ?? '') === 'light-cream' ? 'selected' : '' ?>>Açık Krem</option>
                                    <option value="light-gray" <?= ($currentUser['theme'] ?? '') === 'light-gray' ? 'selected' : '' ?>>Açık Gri</option>
                                </select>
                            </div>

                            <hr class="border-slate-700 my-2">

                            <?php if (!empty($isAdmin)): ?>
                            <a href="<?= base_url('admin') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-purple-400 hover:text-purple-300 hover:bg-slate-700/50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Admin Panel
                            </a>
                            <?php endif; ?>

                            <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-slate-700/50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Çıkış Yap
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login Button -->
                    <a href="<?= base_url('auth/google') ?>" class="btn-primary text-sm py-2 flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="hidden sm:inline">Google ile Giriş</span>
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                    class="sm:hidden p-2 rounded-lg hover:bg-slate-800 transition-colors"
                >
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="pb-3 md:hidden">
            <form action="<?= base_url('projects') ?>" method="get" class="relative">
                <input
                    type="text"
                    name="q"
                    placeholder="Proje ara..."
                    value="<?= esc($filters['search'] ?? '') ?>"
                    class="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                >
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden sm:hidden border-t border-slate-800">
        <div class="px-4 py-3 space-y-2">
            <a href="<?= base_url('projects') ?>" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                Projeler
            </a>
        </div>
    </div>
</nav>

<?php if ($isLoggedIn): ?>
<script>
// Notification functions
let notificationsLoaded = false;

function toggleNotifications() {
    const menu = document.getElementById('notification-menu');
    menu.classList.toggle('hidden');

    if (!menu.classList.contains('hidden') && !notificationsLoaded) {
        loadNotifications();
    }
}

function loadNotifications() {
    fetch('<?= base_url('api/notifications') ?>')
        .then(response => response.json())
        .then(data => {
            notificationsLoaded = true;
            const list = document.getElementById('notification-list');

            if (!data.success || data.notifications.length === 0) {
                list.innerHTML = '<div class="px-4 py-8 text-center text-slate-400 text-sm">Bildirim yok</div>';
                return;
            }

            list.innerHTML = data.notifications.map(n => renderNotification(n)).join('');
            updateNotificationBadge();
        })
        .catch(err => {
            document.getElementById('notification-list').innerHTML = '<div class="px-4 py-8 text-center text-red-400 text-sm">Yüklenemedi</div>';
        });
}

async function markNotificationAsRead(notificationId, event) {
    event.preventDefault();
    const link = event.currentTarget;

    try {
        await fetch('<?= base_url('api/notifications/') ?>' + notificationId + '/read', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        updateNotificationBadge();
        // Remove unread styling
        link.classList.remove('bg-purple-500/10');
    } catch (err) {
        console.error('Mark read failed:', err);
    }

    // Navigate to the link
    window.location.href = link.href;
}

function renderNotification(n) {
    const icon = getNotificationIcon(n.type);
    const message = getNotificationMessage(n);
    const isUnread = !n.is_read ? 'bg-purple-500/10' : '';
    let url;
    if (n.type === 'message') {
        url = '<?= base_url('messages') ?>';
    } else if (n.project_id) {
        url = '<?= base_url('projects') ?>/' + n.project_slug;
    } else {
        url = '<?= base_url('user') ?>/' + n.actor_id;
    }

    return `
        <a href="${url}" onclick="markNotificationAsRead(${n.id}, event)" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-700/50 transition-colors ${isUnread}">
            <img src="${n.actor_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(n.actor_name)}" class="w-8 h-8 rounded-full flex-shrink-0" alt="" referrerpolicy="no-referrer">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-slate-300"><span class="font-medium text-white">${escapeHtml(n.actor_name)}</span> ${message}</p>
                <p class="text-xs text-slate-500 mt-1">${timeAgo(n.created_at)}</p>
            </div>
            ${icon}
        </a>
    `;
}

function getNotificationIcon(type) {
    const icons = {
        'like': '<svg class="w-4 h-4 text-pink-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
        'comment': '<svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
        'follow': '<svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>',
        'approve': '<svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'message': '<svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
    };
    return icons[type] || '';
}

function getNotificationMessage(n) {
    const messages = {
        'like': 'projenizi beğendi',
        'comment': 'projenize yorum yaptı',
        'follow': 'sizi takip etmeye başladı',
        'approve': 'projeniz onaylandı',
        'message': 'size mesaj gönderdi'
    };
    let msg = messages[n.type] || '';
    if (n.type === 'message' && n.content) {
        msg = `size mesaj gönderdi: "${escapeHtml(n.content)}"`;
    } else if (n.project_title && n.type !== 'follow' && n.type !== 'message') {
        msg = `"${escapeHtml(n.project_title)}" ${n.type === 'approve' ? '' : 'adlı '}${msg}`;
    }
    return msg;
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Az önce';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' dk önce';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' saat önce';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' gün önce';
    return date.toLocaleDateString('tr-TR');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load unread count on page load
function updateNotificationBadge() {
    fetch('<?= base_url('api/notifications/unread-count') ?>')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notification-dropdown');
    const menu = document.getElementById('notification-menu');
    if (dropdown && !dropdown.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// Update badge on page load
document.addEventListener('DOMContentLoaded', updateNotificationBadge);

// Message badge update function
function updateMessageBadge() {
    fetch('<?= base_url('api/messages/unread-count') ?>')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('message-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            }
        });
}

// Update message badge on page load
document.addEventListener('DOMContentLoaded', updateMessageBadge);

// Periodically update message badge
setInterval(updateMessageBadge, 30000);

// Theme change function
function changeTheme(theme) {
    // Instantly update the theme for preview
    document.getElementById('html-root').setAttribute('data-theme', theme);

    // Save to server
    fetch('<?= base_url('user/update-theme') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'theme=' + encodeURIComponent(theme)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Tema değiştirilemedi', 'error');
            // Revert theme on error
            location.reload();
        }
    })
    .catch(err => {
        showToast('Tema değiştirilemedi', 'error');
        location.reload();
    });
}
</script>
<?php endif; ?>

<script>
// Dark mode toggle (works for all users)
const lightThemes = ['light-white', 'light-cream', 'light-gray'];
const defaultDarkTheme = 'default';
const defaultLightTheme = 'light-white';

function getCurrentTheme() {
    return document.getElementById('html-root').getAttribute('data-theme') || 'default';
}

function isLightTheme(theme) {
    return lightThemes.includes(theme);
}

function updateThemeIcon() {
    const current = getCurrentTheme();
    const sunIcon = document.getElementById('theme-icon-sun');
    const moonIcon = document.getElementById('theme-icon-moon');

    if (isLightTheme(current)) {
        sunIcon.classList.add('hidden');
        moonIcon.classList.remove('hidden');
    } else {
        sunIcon.classList.remove('hidden');
        moonIcon.classList.add('hidden');
    }
}

function toggleDarkMode() {
    const current = getCurrentTheme();
    const newTheme = isLightTheme(current) ? defaultDarkTheme : defaultLightTheme;

    // Update UI immediately
    document.getElementById('html-root').setAttribute('data-theme', newTheme);
    updateThemeIcon();

    // Save to localStorage for non-logged users
    localStorage.setItem('theme', newTheme);

    // If logged in, also save to server
    <?php if ($isLoggedIn): ?>
    fetch('<?= base_url('user/update-theme') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'theme=' + encodeURIComponent(newTheme)
    });
    <?php endif; ?>
}

// Initialize icon on page load
document.addEventListener('DOMContentLoaded', updateThemeIcon);
</script>
