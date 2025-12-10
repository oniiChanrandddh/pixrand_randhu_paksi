<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/users/add_user.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Tambah Pengguna Baru</h1>
    <p class="subtitle-main">Isi data pengguna untuk menambahkan akun baru</p>

    <div class="form-panel">
        <form action="../actions/user_create.php" method="POST" class="user-form">

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required minlength="4">
            </div>

            <div class="form-group">
                <label>Role</label>

                <div class="custom-select-role" id="roleSelectBox">
                    <div class="custom-select-selected">User</div>
                    <div class="custom-select-arrow"></div>
                    <div class="custom-select-options">
                        <div data-value="admin">Admin</div>
                        <div data-value="user" class="selected">User</div>
                    </div>
                </div>

                <input type="hidden" name="role" id="role" value="user" required>
            </div>

            <div class="form-actions">
                <a href="users.php" class="btn-secondary">
                    <i data-feather="arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i data-feather="check"></i> Simpan Pengguna
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
