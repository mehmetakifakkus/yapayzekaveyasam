<!DOCTYPE html>
<html lang="tr" data-theme="<?= getenv('APP_THEME') ?: 'default' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'AI Showcase') ?></title>
    <meta name="description" content="Yapay zeka araçlarıyla oluşturulmuş web projelerini keşfedin ve paylaşın.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

    <!-- Heroicons -->
    <script src="https://unpkg.com/@heroicons/vue@2.0.18/24/outline/index.js" defer></script>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div id="toast-success" class="toast toast-success">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div id="toast-error" class="toast toast-error">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('components/footer') ?>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>

    <script>
        // Auto-hide toasts
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.style.transform = 'translateY(200%)';
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        });
    </script>
</body>
</html>
