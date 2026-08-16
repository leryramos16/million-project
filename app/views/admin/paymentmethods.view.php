<?php $title = 'Payment Methods'; $activeNav = 'payments'; require __DIR__ . '/_header.php'; ?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title">Payment Methods</h1>
        <p class="gm-subtitle">Shown to players when they submit a quest, so they know where to send payment.</p>
    </div>
    <a href="<?= ROOT ?>/admin/createPaymentMethod" class="gm-btn gm-btn-primary">+ Add Method</a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="gm-alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($methods)): ?>
    <div class="gm-quest-grid">
        <?php foreach ($methods as $method): ?>
            <div class="gm-quest-card">
                <h3><?= htmlspecialchars($method['label']) ?></h3>
                <p><strong><?= htmlspecialchars($method['account_name']) ?></strong></p>
                <p><?= htmlspecialchars($method['account_number']) ?></p>

                <?php if (!empty($method['instructions'])): ?>
                    <p><em><?= htmlspecialchars($method['instructions']) ?></em></p>
                <?php endif; ?>

                <?php if (!empty($method['qr_code_image'])): ?>
                    <div class="gm-quest-proof">
                        <img src="<?= ROOT ?>/uploads/qrcodes/<?= htmlspecialchars($method['qr_code_image']) ?>" alt="QR code">
                    </div>
                <?php endif; ?>

                <div class="gm-quest-meta">
                    <span class="gm-badge <?= $method['is_active'] ? 'difficulty-easy' : 'type' ?>">
                        <?= $method['is_active'] ? 'Active' : 'Hidden' ?>
                    </span>
                </div>

                <div class="gm-quest-actions">
                    <a href="<?= ROOT ?>/admin/editPaymentMethod/<?= $method['id'] ?>" class="gm-btn">Edit</a>
                    <a href="<?= ROOT ?>/admin/deletePaymentMethod/<?= $method['id'] ?>"
                       class="gm-btn"
                       onclick="return confirm('Remove this payment method?');">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="gm-empty">No payment methods yet. Add your GCash, Maya, or bank details so players know where to send payment.</div>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
