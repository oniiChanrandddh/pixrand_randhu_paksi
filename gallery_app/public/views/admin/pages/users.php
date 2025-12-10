<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalAdmins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='admin'"))['total'];

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$searchEscaped = mysqli_real_escape_string($conn, $search);

$searchQuery = $search
    ? "WHERE full_name LIKE '%$searchEscaped%' OR username LIKE '%$searchEscaped%'"
    : "";

$usersQuery = mysqli_query($conn, "
    SELECT * FROM users
    $searchQuery
    ORDER BY created_at DESC
");

$currentUserId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/users/users.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <div class="header-section">
        <h1 class="title-main">Kelola Pengguna</h1>
        <p class="subtitle-main">Manajemen akun pengguna dalam sistem</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i data-feather="users"></i>
            <div>
                <h2><?= $totalUsers ?></h2>
                <span>Total Pengguna</span>
            </div>
        </div>

        <div class="stat-card">
            <i data-feather="shield"></i>
            <div>
                <h2><?= $totalAdmins ?></h2>
                <span>Total Admin</span>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="actions-bar">
            <a href="add_user.php" class="btn-primary">
                <i data-feather="user-plus"></i> Tambah User
            </a>

            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari nama atau username..."
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i data-feather="search"></i></button>
            </form>
        </div>

        <?php if (mysqli_num_rows($usersQuery) == 0): ?>
            <p class="empty">Tidak ada pengguna ditemukan.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    while ($u = mysqli_fetch_assoc($usersQuery)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td>
                                <span class="badge <?= $u['role'] == 'admin' ? 'admin' : 'user' ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td><?= date("d M Y", strtotime($u['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn-edit">
                                        <i data-feather="edit-2"></i>
                                    </a>

                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="../actions/user_delete.php?id=<?= $u['id'] ?>"
                                            class="btn-delete delete-user"
                                            data-id="<?= $u['id'] ?>">
                                            <i data-feather="trash-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-user').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "User akan hilang dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#160626',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../actions/user_delete.php?id=${userId}`;
                }
            });
        });
    });

    feather.replace();
</script>