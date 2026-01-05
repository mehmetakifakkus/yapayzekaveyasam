<footer class="bg-slate-900 border-t border-slate-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="md:col-span-2">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold gradient-text">AI Showcase</span>
                </a>
                <p class="text-slate-400 text-sm max-w-md">
                    Yapay zeka araçlarıyla (Claude Code, Cursor, Windsurf vb.) oluşturulmuş harika web projelerini keşfedin ve kendi projelerinizi paylaşın.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-semibold mb-4">Hızlı Linkler</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= base_url('projects') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            Tüm Projeler
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('projects?sort=trending') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            Trend Projeler
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('projects/create') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            Proje Ekle
                        </a>
                    </li>
                </ul>
            </div>

            <!-- AI Tools -->
            <div>
                <h3 class="text-white font-semibold mb-4">AI Araçları</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= base_url('tool/claude-code') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            Claude Code
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('tool/cursor') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            Cursor
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('tool/windsurf') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            Windsurf
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('tool/v0') ?>" class="text-slate-400 hover:text-white text-sm transition-colors">
                            v0
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-slate-500 text-sm">
                &copy; <?= date('Y') ?> AI Showcase. Tüm hakları saklıdır.
            </p>
            <p class="text-slate-500 text-sm flex items-center gap-1">
                Made with
                <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                by AI
            </p>
        </div>
    </div>
</footer>
