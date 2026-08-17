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
    <div class="gm-stat-card">
        <div class="gm-stat-value"><?= (int) ($pendingCashoutCount ?? 0) ?></div>
        <div class="gm-stat-label">Cashouts Awaiting Payout</div>
    </div>
</div>

<a href="<?= ROOT ?>/admin/viewPendingRequests" class="gm-btn gm-btn-primary">Review Pending Quests</a>
<a href="<?= ROOT ?>/admin/cashouts" class="gm-btn" style="margin-left:10px;">Manage Cashouts</a>

<h2 class="gm-title" style="font-size:22px; margin-top:36px;">Your Reserve</h2>
<p class="gm-subtitle">This is your real financial position — not a guess.</p>

<?php $ledger = $ledger ?? []; ?>
<div class="gm-stat-grid">
    <div class="gm-stat-card">
        <div class="gm-stat-value">₱<?= (int) ($ledger['pesos_collected'] ?? 0) ?></div>
        <div class="gm-stat-label">Collected (approved quests)</div>
    </div>
    <div class="gm-stat-card">
        <div class="gm-stat-value">₱<?= (int) ($ledger['pesos_paid_out'] ?? 0) ?></div>
        <div class="gm-stat-label">Already Paid Out</div>
    </div>
    <div class="gm-stat-card">
        <div class="gm-stat-value">₱<?= (int) ($ledger['pesos_pending_payout'] ?? 0) ?></div>
        <div class="gm-stat-label">Pending Payout Requests</div>
    </div>
    <div class="gm-stat-card">
        <div class="gm-stat-value">₱<?= (int) ($ledger['coins_outstanding'] ?? 0) ?></div>
        <div class="gm-stat-label">Coins in Wallets (you owe this if cashed out)</div>
    </div>
    <div class="gm-stat-card" style="border-color:<?= ($ledger['your_margin'] ?? 0) >= 0 ? '#5f8f3a' : '#8b3a1a' ?>;">
        <div class="gm-stat-value" style="color:<?= ($ledger['your_margin'] ?? 0) >= 0 ? '#8fd66a' : '#ff8a65' ?>;">
            ₱<?= (int) ($ledger['your_margin'] ?? 0) ?>
        </div>
        <div class="gm-stat-label">Your Locked-In Margin</div>
    </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
