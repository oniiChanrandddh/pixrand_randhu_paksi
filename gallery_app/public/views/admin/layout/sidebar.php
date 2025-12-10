<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/components/sidebar.css">

<aside class="admin-sidebar">

    <div class="sidebar-logo">
        Pixrand
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="<?= BASE_URL ?>views/admin/pages/dashboard.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <i data-feather="home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>views/admin/pages/albums.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'albums.php' ? 'active' : '' ?>">
                <i data-feather="book"></i>
                <span>Albums</span>
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>views/admin/pages/photos.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'photos.php' ? 'active' : '' ?>">
                <i data-feather="image"></i>
                <span>Photos</span>
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>views/admin/pages/users.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
                <i data-feather="users"></i>
                <span>Users</span>
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>views/admin/pages/comments.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'comments.php' ? 'active' : '' ?>">
                <i data-feather="message-square"></i>
                <span>Comments</span>
            </a>
        </li>

        <li>
            <li>
    <a href="<?= BASE_URL ?>logout.php" 
       class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : '' ?>">
        <i data-feather="log-out"></i>
        <span>Logout</span>
    </a>
</li>

        </li>

    </ul>

</aside>

<script>
    feather.replace();
</script>
