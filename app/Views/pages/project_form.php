<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">
            <?= $isEdit ? 'Projeyi Düzenle' : 'Yeni Proje Ekle' ?>
        </h1>
        <p class="text-slate-400">
            <?= $isEdit ? 'Proje bilgilerinizi güncelleyin.' : 'AI araçlarıyla yaptığınız harika projenizi paylaşın.' ?>
        </p>
    </div>

    <!-- Validation Errors -->
    <?php if (session()->has('errors')): ?>
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-xl">
        <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-xl">
        <p class="text-red-300 text-sm"><?= esc(session('error')) ?></p>
    </div>
    <?php endif; ?>

    <!-- Form -->
    <form
        action="<?= $isEdit ? base_url('projects/' . $project['slug'] . '/update') : base_url('projects/store') ?>"
        method="POST"
        enctype="multipart/form-data"
        class="glass-card p-6 sm:p-8 space-y-6"
    >
        <?= csrf_field() ?>

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-slate-300 mb-2">
                Proje Adı <span class="text-red-400">*</span>
            </label>
            <input
                type="text"
                name="title"
                id="title"
                value="<?= esc(old('title', $project['title'] ?? '')) ?>"
                placeholder="Örn: E-Ticaret Dashboard"
                class="input-field"
                required
                maxlength="255"
            >
        </div>

        <!-- Category -->
        <div>
            <label for="category_id" class="block text-sm font-medium text-slate-300 mb-2">
                Kategori <span class="text-red-400">*</span>
            </label>
            <select name="category_id" id="category_id" class="select-field" required>
                <option value="">Kategori Seçin</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= old('category_id', $project['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- AI Tools -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Kullanılan AI Araçları <span class="text-red-400">*</span>
            </label>
            <p class="text-xs text-slate-500 mb-3">Projenizi oluştururken kullandığınız AI araçlarını seçin (birden fazla seçebilirsiniz)</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <?php
                $selectedTools = old('ai_tools', $project['ai_tool_ids'] ?? []);
                foreach ($aiTools as $tool):
                ?>
                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-800/50 border border-slate-700 cursor-pointer hover:border-purple-500/50 transition-colors has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                    <input
                        type="checkbox"
                        name="ai_tools[]"
                        value="<?= $tool['id'] ?>"
                        <?= in_array($tool['id'], (array)$selectedTools) ? 'checked' : '' ?>
                        class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-purple-500 focus:ring-purple-500/50"
                    >
                    <span class="text-sm text-slate-300"><?= esc($tool['name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tags -->
        <div>
            <label for="tags" class="block text-sm font-medium text-slate-300 mb-2">
                Etiketler <span class="text-slate-500">(Opsiyonel)</span>
            </label>
            <input
                type="text"
                name="tags"
                id="tags"
                value="<?= esc(old('tags', $project['tags_string'] ?? '')) ?>"
                placeholder="react, typescript, dashboard (virgülle ayırın)"
                class="input-field"
            >
            <p class="text-xs text-slate-500 mt-1">Projenizi tanımlayan etiketleri virgülle ayırarak yazın</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-slate-300 mb-2">
                Açıklama <span class="text-red-400">*</span>
            </label>
            <textarea
                name="description"
                id="description"
                rows="5"
                placeholder="Projeniz hakkında detaylı bilgi verin. Ne yapıyor? Nasıl çalışıyor? Hangi teknolojileri kullanıyor?"
                class="textarea-field"
                required
                minlength="10"
            ><?= esc(old('description', $project['description'] ?? '')) ?></textarea>
            <p class="text-xs text-slate-500 mt-1">En az 10 karakter</p>
        </div>

        <!-- Website URL -->
        <div>
            <label for="website_url" class="block text-sm font-medium text-slate-300 mb-2">
                Web Sitesi URL <span class="text-red-400">*</span>
            </label>
            <input
                type="url"
                name="website_url"
                id="website_url"
                value="<?= esc(old('website_url', $project['website_url'] ?? '')) ?>"
                placeholder="https://projeniz.com"
                class="input-field"
                required
            >
        </div>

        <!-- GitHub URL -->
        <div>
            <label for="github_url" class="block text-sm font-medium text-slate-300 mb-2">
                GitHub URL <span class="text-slate-500">(Opsiyonel)</span>
            </label>
            <input
                type="url"
                name="github_url"
                id="github_url"
                value="<?= esc(old('github_url', $project['github_url'] ?? '')) ?>"
                placeholder="https://github.com/kullanici/proje"
                class="input-field"
            >
        </div>

        <!-- Screenshot -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Ekran Görüntüsü <span class="text-slate-500">(Opsiyonel)</span>
            </label>
            <p class="text-xs text-emerald-400 mb-3 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Yüklemezseniz, web sitenizin ekran görüntüsü otomatik olarak alınacaktır.
            </p>

            <?php if ($isEdit && !empty($project['screenshot'])): ?>
            <div class="mb-3">
                <img src="<?= base_url($project['screenshot']) ?>" alt="Mevcut ekran görüntüsü" class="max-h-48 rounded-xl">
                <p class="text-xs text-slate-500 mt-2">Yeni bir resim yüklerseniz mevcut resim değiştirilecektir.</p>
            </div>
            <?php endif; ?>

            <div class="relative">
                <input
                    type="file"
                    name="screenshot"
                    id="screenshot"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    onchange="previewImage(this)"
                >
                <div id="upload-area" class="border-2 border-dashed border-slate-700 rounded-xl p-8 text-center hover:border-purple-500/50 transition-colors">
                    <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-slate-400 text-sm mb-1">Tıklayın, sürükleyin veya yapıştırın (Ctrl+V)</p>
                    <p class="text-slate-600 text-xs">PNG, JPG, GIF, WEBP - Maks 5MB</p>
                </div>
            </div>

            <div id="preview-container" class="hidden mt-3">
                <img id="preview-image" src="" alt="Önizleme" class="max-h-48 rounded-xl">
                <button type="button" onclick="clearPreview()" class="text-sm text-red-400 hover:text-red-300 mt-2">
                    Resmi Kaldır
                </button>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <?= $isEdit ? 'Güncelle' : 'Projeyi Ekle' ?>
            </button>
            <a href="<?= $isEdit ? base_url('projects/' . $project['slug']) : base_url('projects') ?>" class="btn-secondary">
                İptal
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('Dosya boyutu 5MB\'dan küçük olmalıdır.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('preview-container').classList.remove('hidden');
            document.getElementById('upload-area').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function clearPreview() {
    document.getElementById('screenshot').value = '';
    document.getElementById('preview-container').classList.add('hidden');
    document.getElementById('upload-area').classList.remove('hidden');
}

// Clipboard paste desteği
document.addEventListener('paste', function(e) {
    const items = e.clipboardData?.items;
    if (!items) return;

    for (let item of items) {
        if (item.type.startsWith('image/')) {
            const file = item.getAsFile();
            if (file) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('screenshot').files = dataTransfer.files;
                previewImage(document.getElementById('screenshot'));
                e.preventDefault();
                break;
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
