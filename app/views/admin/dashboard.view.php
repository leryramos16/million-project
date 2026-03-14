<h1>Admin Dashboard</h1>

<p>Welcome Admin <?= $_SESSION['username'] ?></p>

<ul>
    <li><a href="<?= ROOT ?>/admin/users">Manage Users</a></li>
    <li><a href="<?= ROOT ?>/admin/bookings">Manage Bookings</a></li>
    <li><a href="<?= ROOT ?>/logout">Logout</a></li>
</ul>