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

                <?php if ($isLoggedIn): ?>
                    <!-- Add Project Button -->
                    <a href="<?= base_url('projects/create') ?>" class="btn-primary text-sm py-2 hidden sm:flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Proje Ekle</span>
                    </a>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button
                            onclick="this.nextElementSibling.classList.toggle('hidden')"
                            class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-800 transition-colors"
                        >
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?= esc($currentUser['avatar']) ?>" alt="<?= esc($currentUser['name']) ?>" class="w-8 h-8 rounded-full object-cover">
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

                            <a href="<?= base_url('projects/create') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors sm:hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Proje Ekle
                            </a>

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
