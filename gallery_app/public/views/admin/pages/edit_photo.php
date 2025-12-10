<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

if (!isset($_GET['id'])) {
    header("Location: photos.php");
    exit;
}

$id = intval($_GET['id']);

$query = mysqli_query($conn, "
    SELECT photos.*, albums.title AS album_title, albums.visibility 
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE photos.id = $id 
      AND albums.visibility = 'public'
");

$photo = mysqli_fetch_assoc($query);

if (!$photo) {
    header("Location: photos.php?error=unauthorized");
    exit;
}

$albumQuery = mysqli_query($conn, "
    SELECT id, title 
    FROM albums 
    WHERE visibility = 'public'
    ORDER BY title ASC
");
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/photos/add_photo.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Edit Foto Publik</h1>
    <p class="subtitle-main">Admin hanya dapat mengedit foto dari album publik</p>

    <div class="panel">
        <form action="../actions/photo_update.php" method="POST" enctype="multipart/form-data" class="form-grid">

            <input type="hidden" name="id" value="<?= $photo['id'] ?>">

            <div class="form-group">
                <label>Judul Foto <span class="required">*</span></label>
                <input type="text" name="title" class="form-input"
                    placeholder="Masukkan judul foto" maxlength="100"
                    value="<?= htmlspecialchars($photo['title']) ?>" required>
            </div>

            <div class="form-group">
                <label>Foto Saat Ini</label>
                <img src="/gallery_app/public/uploads/<?= htmlspecialchars($photo['file_path']) ?>" class="preview-image" style="display:block;">
            </div>

            <div class="form-group">
                <label>Ganti Foto (opsional)</label>

                <div class="file-wrapper">
                    <button type="button" class="file-btn" id="fileTrigger">Pilih Foto Baru</button>
                    <span class="file-name" id="fileName">Tidak ada file dipilih</span>
                    <input type="file" name="photo" id="photoInput" accept="image/*" hidden>
                </div>

                <img id="previewImg" class="preview-image" style="display:none;">
                <button type="button" id="cancelPreview" class="btn-cancel-preview" style="display:none;">Batalkan</button>
                <p class="info-text">Format: JPG, PNG</p>
            </div>

            <div class="form-group">
                <label>Album (Hanya Publik)</label>

                <div class="custom-select-album" id="albumSelectBox">
                    <div class="custom-select-selected"><?= htmlspecialchars($photo['album_title']) ?></div>
                    <div class="custom-select-arrow"></div>
                    <div class="custom-select-options">
                        <?php while($alb = mysqli_fetch_assoc($albumQuery)): ?>
                            <div data-value="<?= $alb['id'] ?>"><?= htmlspecialchars($alb['title']) ?></div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <input type="hidden" name="album_id" id="album_id" value="<?= $photo['album_id'] ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi (opsional)</label>
                <textarea name="caption" maxlength="255"><?= htmlspecialchars($photo['caption']) ?></textarea>
            </div>

            <div class="form-actions">
                <a href="photos.php" class="btn-secondary">
                    <i data-feather="arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>

<script>
feather.replace();

const fileTrigger = document.getElementById('fileTrigger');
const photoInput = document.getElementById('photoInput');
const fileName = document.getElementById('fileName');
const previewImg = document.getElementById('previewImg');
const cancelPreview = document.getElementById('cancelPreview');

fileTrigger.addEventListener('click', () => photoInput.click());
photoInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        fileName.textContent = this.files[0].name;
        previewImg.src = URL.createObjectURL(this.files[0]);
        previewImg.style.display = "block";
        cancelPreview.style.display = "inline-block";
    }
});

cancelPreview.addEventListener('click', function () {
    photoInput.value = "";
    previewImg.style.display = "none";
    fileName.textContent = "Tidak ada file dipilih";
    cancelPreview.style.display = "none";
});

const selectBox = document.getElementById('albumSelectBox');
const selectedDisplay = selectBox.querySelector('.custom-select-selected');
const optionsSelect = selectBox.querySelector('.custom-select-options');
const albumInput = document.getElementById('album_id');

selectedDisplay.addEventListener('click', e => {
    e.stopPropagation();
    selectBox.classList.toggle('open');
});

optionsSelect.querySelectorAll('div').forEach(option => {
    option.addEventListener('click', e => {
        selectedDisplay.textContent = option.textContent;
        albumInput.value = option.dataset.value;
        optionsSelect.querySelectorAll('div').forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');
        selectBox.classList.remove('open');
    });
});

document.addEventListener('click', () => selectBox.classList.remove('open'));
</script>
