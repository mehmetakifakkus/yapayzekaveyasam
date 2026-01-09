<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-lg w-full">
        <?php if ($project['status'] === 'pending'): ?>
            <!-- Pending Status -->
            <div class="glass-card p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-white mb-3">Onay Bekliyor</h1>

                <p class="text-gray-400 mb-6">
                    <span class="text-white font-semibold">"<?= esc($project['title']) ?>"</span>
                    projeniz inceleme aşamasındadır. En kısa sürede ekibimiz tarafından gözden geçirilecektir.
                </p>

                <div class="bg-white/5 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Gönderilme Tarihi</span>
                        <span class="text-gray-300"><?= date('d.m.Y H:i', strtotime($project['created_at'])) ?></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-left p-3 bg-white/5 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-400">Proje başarıyla gönderildi</span>
                    </div>

                    <div class="flex items-center gap-3 text-left p-3 bg-amber-500/10 rounded-lg border border-amber-500/20">
                        <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-amber-300">İnceleme bekleniyor</span>
                    </div>

                    <div class="flex items-center gap-3 text-left p-3 bg-white/5 rounded-lg opacity-50">
                        <div class="w-8 h-8 rounded-full bg-gray-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-500">Yayınlanacak</span>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <a href="/" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Ana Sayfaya Dön
                    </a>
                </div>
            </div>

        <?php elseif ($project['status'] === 'rejected'): ?>
            <!-- Rejected Status -->
            <div class="glass-card p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-500/20 flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-white mb-3">Proje Reddedildi</h1>

                <p class="text-gray-400 mb-6">
                    <span class="text-white font-semibold">"<?= esc($project['title']) ?>"</span>
                    projeniz maalesef onaylanmadı.
                </p>

                <?php if (!empty($project['rejection_reason'])): ?>
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-6 text-left">
                        <p class="text-sm text-gray-500 mb-1">Red Nedeni:</p>
                        <p class="text-red-300"><?= esc($project['rejection_reason']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8 pt-6 border-t border-white/10">
                    <a href="/projects/<?= esc($project['slug']) ?>/edit" class="btn-primary inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Düzenle ve Tekrar Gönder
                    </a>
                    <a href="/" class="btn-secondary inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Ana Sayfa
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
