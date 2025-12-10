<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

$userId = intval($_SESSION['user']['id']);
$albumsPublic = mysqli_query($conn, "SELECT id, title FROM albums WHERE visibility='public' ORDER BY title ASC");
$albumsPrivate = mysqli_query($conn, "SELECT id, title FROM albums WHERE visibility='private' AND user_id=$userId ORDER BY title ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    if ($title === "") $title = "Tanpa Judul";

    $caption = mysqli_real_escape_string($conn, trim($_POST['caption']));
    $albumId = intval($_POST['album_id'] ?? 0);
    $albumType = $_POST['album_type'] ?? '';

    if ($albumType === '' || $albumId === 0 || empty($_FILES['file']['name'])) {
        header("Location: add_photo.php?error=1");
        exit;
    }

    $allowedExt = ['jpg','jpeg','png'];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        header("Location: add_photo.php?error=format");
        exit;
    }

    $fileName = time() . '_' . uniqid() . "." . $ext;
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $fileName;

    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath)) {
        header("Location: add_photo.php?error=upload");
        exit;
    }

    mysqli_query($conn, "
        INSERT INTO photos (user_id, album_id, file_path, title, caption, created_at)
        VALUES ($userId, $albumId, '$fileName', '$title', '$caption', NOW())
    ");

    header("Location: photos.php?success=1");
    exit;
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/photos/add_photo.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">
    <h2 class="page-title">Upload Foto</h2>

    <form action="" method="POST" enctype="multipart/form-data" class="upload-form">

        <div class="form-group">
            <label>Judul Foto *</label>
            <input type="text" name="title" placeholder="Masukkan judul foto">
        </div>

        <div class="form-group">
            <label>Jenis Album *</label>
            <div class="custom-select" id="typeSelect">
                <div class="selected">Pilih jenis album...</div>
                <ul class="options">
                    <li data-value="public">Album Publik</li>
                    <li data-value="private">Album Pribadi</li>
                </ul>
            </div>
            <input type="hidden" name="album_type" id="album_type" required>
        </div>

        <div class="form-group">
            <label>Pilih Album *</label>
            <div class="custom-select disabled" id="albumSelect">
                <div class="selected">Pilih jenis album terlebih dahulu</div>
                <ul class="options"></ul>
            </div>
            <input type="hidden" name="album_id" id="album_id" required>
        </div>

        <div class="form-group">
            <label>Foto *</label>
            <input type="file" name="file" id="fileInput" accept="image/*" required>
            <img id="previewImage" class="preview-box">
        </div>

        <div class="form-group">
            <label>Caption</label>
            <textarea name="caption" rows="3" placeholder="Tambahkan deskripsi..."></textarea>
        </div>

        <button type="submit" class="btn-primary">
            <i data-feather="upload"></i> Upload
        </button>
    </form>
</div>
<?php require_once __DIR__ . "/../layout/footer.php"; ?>
<script>
feather.replace();
const typeSelect = document.getElementById("typeSelect");
const typeSelected = typeSelect.querySelector(".selected");
const typeOptions = typeSelect.querySelectorAll(".options li");
const hiddenType = document.getElementById("album_type");
const albumSelect = document.getElementById("albumSelect");
const albumSelected = albumSelect.querySelector(".selected");
const albumOptionsBox = albumSelect.querySelector(".options");
const hiddenAlbum = document.getElementById("album_id");
const previewImage = document.getElementById("previewImage");
const fileInput = document.getElementById("fileInput");
const albumsPublic = <?= json_encode(mysqli_fetch_all($albumsPublic, MYSQLI_ASSOC)) ?>;
const albumsPrivate = <?= json_encode(mysqli_fetch_all($albumsPrivate, MYSQLI_ASSOC)) ?>;

function resetAlbum(t) {
    albumSelected.textContent = t;
    albumOptionsBox.innerHTML = "";
    hiddenAlbum.value = "";
    albumSelect.classList.add("disabled");
}

typeOptions.forEach(opt => {
    opt.addEventListener("click", e => {
        e.stopPropagation();
        hiddenType.value = opt.dataset.value;
        typeSelected.textContent = opt.textContent;
        typeSelect.classList.remove("active");
        const list = opt.dataset.value === "public" ? albumsPublic : albumsPrivate;

        if (!list.length) {
            resetAlbum("Tidak ada album tersedia");
            return;
        }

        albumSelect.classList.remove("disabled");
        albumSelected.textContent = "Pilih Album";
        albumOptionsBox.innerHTML = "";

        list.forEach(a => {
            const li = document.createElement("li");
            li.textContent = a.title;
            li.addEventListener("click", ev => {
                ev.stopPropagation();
                albumSelected.textContent = a.title;
                hiddenAlbum.value = a.id;
                albumSelect.classList.remove("active");
            });
            albumOptionsBox.appendChild(li);
        });
    });
});

typeSelected.addEventListener("click", e => {
    e.stopPropagation();
    typeSelect.classList.toggle("active");
});

albumSelected.addEventListener("click", e => {
    e.stopPropagation();
    if (!albumSelect.classList.contains("disabled")) {
        albumSelect.classList.toggle("active");
    }
});

document.addEventListener("click", () => {
    typeSelect.classList.remove("active");
    albumSelect.classList.remove("active");
});

fileInput.addEventListener("change", e => {
    const f = e.target.files[0];
    if (!f) return previewImage.style.display = "none";
    const rd = new FileReader();
    rd.onload = ev => {
        previewImage.src = ev.target.result;
        previewImage.style.display = "block";
    };
    rd.readAsDataURL(f);
});
</script>
