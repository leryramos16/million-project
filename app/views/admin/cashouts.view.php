<?php $title = 'Cashouts'; $activeNav = 'cashouts'; require __DIR__ . '/_header.php'; ?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title">Cashout Requests</h1>
        <p class="gm-subtitle">Review and pay out — 1 coin = ₱1. Coins were already deducted when requested.</p>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="gm-alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="gm-stat-grid" style="margin-bottom:28px;">
    <div class="gm-stat-card">
        <div class="gm-stat-value">₱<?= (int) ($ledger['pesos_pending_payout'] ?? 0) ?></div>
        <div class="gm-stat-label">Total Pending Payout</div>
    </div>
    <div class="gm-stat-card">
        <div class="gm-stat-value">₱<?= (int) ($ledger['your_margin'] ?? 0) ?></div>
        <div class="gm-stat-label">Your Locked-In Margin</div>
    </div>
</div>

<?php if (!empty($requests)): ?>
    <table class="gm-table">
        <thead>
            <tr>
                <th>Player</th>
                <th>Coins</th>
                <th>Peso Value</th>
                <th>Method</th>
                <th>Account</th>
                <th>Requested</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= (int) $r['coins_requested'] ?></td>
                    <td>₱<?= (int) $r['peso_amount'] ?></td>
                    <td><?= htmlspecialchars(strtoupper($r['payment_method'])) ?></td>
                    <td><?= htmlspecialchars($r['account_name']) ?> — <?= htmlspecialchars($r['account_number']) ?></td>
                    <td><?= htmlspecialchars($r['requested_at']) ?></td>
                    <td>
                        <div class="gm-quest-actions">
                            <a href="<?= ROOT ?>/admin/payCashout/<?= $r['id'] ?>" class="gm-btn gm-btn-primary"
                               onclick="return confirm('Confirm you already sent ₱<?= (int) $r['peso_amount'] ?> to <?= htmlspecialchars($r['account_number']) ?>?');">
                               Mark Paid
                            </a>
                            <a href="#" class="gm-btn" onclick="document.getElementById('reject-<?= $r['id'] ?>').style.display='flex'; return false;">Reject</a>
                        </div>
                        <form id="reject-<?= $r['id'] ?>" method="POST" action="<?= ROOT ?>/admin/rejectCashout/<?= $r['id'] ?>" style="display:none; gap:6px; margin-top:8px;">
                            <input type="text" name="note" placeholder="Reason (optional)" style="padding:6px; border-radius:6px; border:1px solid #7a4a22;">
                            <button type="submit" class="gm-btn" style="margin-top:6px;">Confirm Reject &amp; Refund Coins</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="gm-empty">No pending cashout requests.</div>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
