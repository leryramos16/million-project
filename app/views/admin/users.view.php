<?php $title = 'Adventurers'; $activeNav = 'users'; require __DIR__ . '/_header.php'; ?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title">Adventurers</h1>
        <p class="gm-subtitle">Every hero registered on the notice board.</p>
    </div>
</div>

<?php if (!empty($users)): ?>
    <table class="gm-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Level</th>
                <th>XP</th>
                <th>Coins</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><span class="gm-role <?= htmlspecialchars($user['role']) ?>"><?= htmlspecialchars($user['role']) ?></span></td>
                    <td><?= (int) $user['level'] ?></td>
                    <td><?= (int) $user['xp'] ?></td>
                    <td><?= (int) $user['coins'] ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="gm-empty">No adventurers yet.</div>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
