<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold">Site Ayarları</h1>
            <p class="opacity-60 mt-1">Tema ve görünüm ayarlarını yönetin</p>
        </div>
        <a href="<?= base_url('admin') ?>" class="btn-secondary">
            ← Dashboard
        </a>
    </div>

    <!-- Theme Settings -->
    <div class="glass-card p-6 mb-8">
        <h2 class="text-xl font-semibold mb-6">Renk Teması</h2>

        <!-- Dark Themes -->
        <h3 class="text-sm font-medium opacity-60 mb-3 uppercase tracking-wide">Koyu Temalar</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Default Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="default" <?= $currentTheme === 'default' ? 'checked' : '' ?> class="hidden peer">
                <div class="glass-card p-4 border-2 border-transparent peer-checked:border-purple-500 transition-all hover:opacity-80">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        <span class="font-medium">Varsayılan</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-indigo-500"></div>
                        <div class="w-6 h-6 rounded bg-purple-500"></div>
                        <div class="w-6 h-6 rounded bg-pink-500"></div>
                    </div>
                    <p class="text-xs opacity-50 mt-2">Mor-Pembe gradyan</p>
                </div>
            </label>

            <!-- Emerald Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="emerald" <?= $currentTheme === 'emerald' ? 'checked' : '' ?> class="hidden peer">
                <div class="glass-card p-4 border-2 border-transparent peer-checked:border-emerald-500 transition-all hover:opacity-80">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400"></div>
                        <span class="font-medium">Mint & Emerald</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-emerald-500"></div>
                        <div class="w-6 h-6 rounded bg-teal-500"></div>
                        <div class="w-6 h-6 rounded bg-cyan-500"></div>
                    </div>
                    <p class="text-xs opacity-50 mt-2">Ferah & Modern</p>
                </div>
            </label>

            <!-- Amber Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="amber" <?= $currentTheme === 'amber' ? 'checked' : '' ?> class="hidden peer">
                <div class="glass-card p-4 border-2 border-transparent peer-checked:border-amber-500 transition-all hover:opacity-80">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-red-500"></div>
                        <span class="font-medium">Amber & Orange</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-amber-500"></div>
                        <div class="w-6 h-6 rounded bg-orange-500"></div>
                        <div class="w-6 h-6 rounded bg-red-500"></div>
                    </div>
                    <p class="text-xs opacity-50 mt-2">Sıcak & Enerjik</p>
                </div>
            </label>

            <!-- Ocean Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="ocean" <?= $currentTheme === 'ocean' ? 'checked' : '' ?> class="hidden peer">
                <div class="glass-card p-4 border-2 border-transparent peer-checked:border-sky-500 transition-all hover:opacity-80">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500"></div>
                        <span class="font-medium">Ocean Blue</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-sky-500"></div>
                        <div class="w-6 h-6 rounded bg-blue-500"></div>
                        <div class="w-6 h-6 rounded bg-indigo-500"></div>
                    </div>
                    <p class="text-xs opacity-50 mt-2">Sakin & Profesyonel</p>
                </div>
            </label>

            <!-- Mono Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="mono" <?= $currentTheme === 'mono' ? 'checked' : '' ?> class="hidden peer">
                <div class="glass-card p-4 border-2 border-transparent peer-checked:border-slate-400 transition-all hover:opacity-80">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-slate-200 via-slate-400 to-slate-600"></div>
                        <span class="font-medium">Mono Minimal</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-slate-300"></div>
                        <div class="w-6 h-6 rounded bg-slate-500"></div>
                        <div class="w-6 h-6 rounded bg-slate-700"></div>
                    </div>
                    <p class="text-xs opacity-50 mt-2">Sade & Minimal</p>
                </div>
            </label>
        </div>

        <!-- Light Themes -->
        <h3 class="text-sm font-medium opacity-60 mb-3 uppercase tracking-wide">Açık Temalar</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Light White Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="light-white" <?= $currentTheme === 'light-white' ? 'checked' : '' ?> class="hidden peer">
                <div class="p-4 border-2 border-transparent peer-checked:border-indigo-500 transition-all hover:opacity-80 rounded-2xl bg-white text-slate-800 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-500"></div>
                        <span class="font-medium text-slate-800">Clean White</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-indigo-500"></div>
                        <div class="w-6 h-6 rounded bg-violet-500"></div>
                        <div class="w-6 h-6 rounded bg-purple-500"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Saf Beyaz & Minimalist</p>
                </div>
            </label>

            <!-- Light Cream Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="light-cream" <?= $currentTheme === 'light-cream' ? 'checked' : '' ?> class="hidden peer">
                <div class="p-4 border-2 border-transparent peer-checked:border-orange-500 transition-all hover:opacity-80 rounded-2xl bg-[#faf8f5] text-stone-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-orange-500 via-orange-400 to-amber-400"></div>
                        <span class="font-medium text-stone-700">Warm Cream</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-orange-600"></div>
                        <div class="w-6 h-6 rounded bg-orange-500"></div>
                        <div class="w-6 h-6 rounded bg-amber-400"></div>
                    </div>
                    <p class="text-xs text-stone-500 mt-2">Sıcak Krem Tonları</p>
                </div>
            </label>

            <!-- Light Gray Theme -->
            <label class="theme-option cursor-pointer">
                <input type="radio" name="theme" value="light-gray" <?= $currentTheme === 'light-gray' ? 'checked' : '' ?> class="hidden peer">
                <div class="p-4 border-2 border-transparent peer-checked:border-sky-500 transition-all hover:opacity-80 rounded-2xl bg-slate-100 text-slate-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-sky-500 via-blue-500 to-indigo-500"></div>
                        <span class="font-medium text-slate-700">Cool Gray</span>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-6 h-6 rounded bg-sky-500"></div>
                        <div class="w-6 h-6 rounded bg-blue-500"></div>
                        <div class="w-6 h-6 rounded bg-indigo-500"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Profesyonel Gri</p>
                </div>
            </label>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm opacity-60">
                Tema değişikliği .env dosyasına kaydedilir ve tüm site için geçerli olur.
            </p>
            <button onclick="saveTheme()" class="btn-primary">
                Kaydet
            </button>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="glass-card p-6">
        <h2 class="text-xl font-semibold mb-4">Önizleme</h2>
        <p class="opacity-60 mb-6">Seçtiğiniz temanın nasıl görüneceğini buradan görebilirsiniz.</p>

        <div class="space-y-4">
            <div>
                <span class="text-sm opacity-50 block mb-2">Gradient Text:</span>
                <h3 class="text-2xl font-bold gradient-text">AI Showcase Platform</h3>
            </div>

            <div>
                <span class="text-sm opacity-50 block mb-2">Butonlar:</span>
                <div class="flex gap-3">
                    <button class="btn-primary">Primary Button</button>
                    <button class="btn-secondary">Secondary Button</button>
                </div>
            </div>

            <div>
                <span class="text-sm opacity-50 block mb-2">Badges:</span>
                <div class="flex gap-2">
                    <span class="category-badge">Kategori</span>
                    <span class="ai-badge">AI Tool</span>
                </div>
            </div>

            <div>
                <span class="text-sm opacity-50 block mb-2">Input:</span>
                <input type="text" class="input-field max-w-xs" placeholder="Örnek input...">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Live preview when selecting theme
document.querySelectorAll('input[name="theme"]').forEach(input => {
    input.addEventListener('change', function() {
        document.documentElement.setAttribute('data-theme', this.value);
    });
});

async function saveTheme() {
    const selected = document.querySelector('input[name="theme"]:checked');
    if (!selected) return;

    const formData = new FormData();
    formData.append('theme', selected.value);

    try {
        const response = await fetch('<?= base_url('admin/settings') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showToast('Tema kaydedildi! Değişikliğin tam olarak uygulanması için sayfayı yenileyin.', 'success');
        } else {
            showToast(data.message || 'Bir hata oluştu', 'error');
        }
    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    }
}
</script>
<?= $this->endSection() ?>
