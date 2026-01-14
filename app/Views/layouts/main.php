<!DOCTYPE html>
<?php
// User theme takes priority, then admin theme, then default
$theme = 'default';
if (!empty($currentUser['theme'])) {
    $theme = $currentUser['theme'];
} elseif (getenv('APP_THEME')) {
    $theme = getenv('APP_THEME');
}
?>
<html lang="tr" data-theme="<?= esc($theme) ?>" id="html-root">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-S6ZZVW715V"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-S6ZZVW715V');
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'AI Showcase') ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>?v=3">
    <link rel="apple-touch-icon" href="<?= base_url('favicon.svg') ?>?v=3">
    <?php
    $metaDescription = $ogDescription ?? 'Yapay zeka araçlarıyla oluşturulmuş web projelerini keşfedin ve paylaşın.';
    $metaTitle = $ogTitle ?? $title ?? 'AI Showcase';
    $metaImage = $ogImage ?? 'https://placehold.co/1200x630/1e1b4b/a78bfa?text=AI+Showcase';
    $metaUrl = $ogUrl ?? current_url();
    $metaType = $ogType ?? 'website';
    ?>
    <meta name="description" content="<?= esc($metaDescription) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="<?= esc($metaType) ?>">
    <meta property="og:url" content="<?= esc($metaUrl) ?>">
    <meta property="og:title" content="<?= esc($metaTitle) ?>">
    <meta property="og:description" content="<?= esc($metaDescription) ?>">
    <meta property="og:image" content="<?= esc($metaImage) ?>">
    <meta property="og:site_name" content="AI Showcase">
    <meta property="og:locale" content="tr_TR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= esc($metaUrl) ?>">
    <meta name="twitter:title" content="<?= esc($metaTitle) ?>">
    <meta name="twitter:description" content="<?= esc($metaDescription) ?>">
    <meta name="twitter:image" content="<?= esc($metaImage) ?>">

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
