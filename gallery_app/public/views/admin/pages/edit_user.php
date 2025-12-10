<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: users.php?error=notfound");
    exit;
}
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/users/add_user.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Edit Pengguna</h1>
    <p class="subtitle-main">Perbarui informasi pengguna</p>

    <div class="form-panel">
        <form action="../actions/user_update.php" method="POST" class="user-form">
            
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" 
                    value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                    value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label>Password Baru (opsional)</label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak mengubah password">
            </div>

            <div class="form-group">
                <label>Role</label>
                <div class="custom-select-role" id="roleSelectBox">
                    <div class="custom-select-selected">
                        <?= ($user['role'] === 'admin') ? 'Admin' : 'User' ?>
                    </div>
                    <div class="custom-select-arrow"></div>
                    <div class="custom-select-options">
                        <div data-value="admin" class="<?= ($user['role'] === 'admin') ? 'selected' : '' ?>">Admin</div>
                        <div data-value="user" class="<?= ($user['role'] === 'user') ? 'selected' : '' ?>">User</div>
                    </div>
                </div>
                <input type="hidden" name="role" id="role" value="<?= $user['role'] ?>" required>
            </div>

            <div class="form-actions">
                <a href="users.php" class="btn-secondary">
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

const roleBox = document.getElementById('roleSelectBox');
const selectedRole = roleBox.querySelector('.custom-select-selected');
const roleOptions = roleBox.querySelector('.custom-select-options');
const roleInput = document.getElementById('role');

selectedRole.addEventListener('click', (e) => {
    e.stopPropagation();
    roleBox.classList.toggle('open');
});

roleOptions.querySelectorAll('div').forEach(option => {
    option.addEventListener('click', (e) => {
        e.stopPropagation();
        selectedRole.textContent = option.textContent;
        roleInput.value = option.dataset.value;
        roleOptions.querySelectorAll('div')
            .forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');
        roleBox.classList.remove('open');
    });
});

document.addEventListener('click', function () {
    roleBox.classList.remove('open');
});
</script>
