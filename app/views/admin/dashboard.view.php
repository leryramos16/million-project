<?php $title = 'Dashboard'; $activeNav = 'dashboard'; require __DIR__ . '/_header.php'; ?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title">Game Master Console</h1>
        <p class="gm-subtitle">Oversee the notice board, adventurers, and pending contracts.</p>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="gm-alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="gm-stat-grid">
    <div class="gm-stat-card">
        <div class="gm-stat-value"><?= (int) ($stats['pending_quests'] ?? 0) ?></div>
        <div class="gm-stat-label">Pending Quests</div>
    </div>
    <div class="gm-stat-card">
        <div class="gm-stat-value"><?= (int) ($stats['total_users'] ?? 0) ?></div>
        <div class="gm-stat-label">Adventurers</div>
    </div>
    <div class="gm-stat-card">
        <div class="gm-stat-value"><?= (int) ($stats['completed_today'] ?? 0) ?></div>
        <div class="gm-stat-label">Completed Today</div>
    </div>
</div>

<a href="<?= ROOT ?>/admin/viewPendingRequests" class="gm-btn gm-btn-primary">Review Pending Quests</a>

<?php require __DIR__ . '/_footer.php'; ?>
