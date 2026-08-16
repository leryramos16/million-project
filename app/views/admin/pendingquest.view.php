<?php $title = 'Pending Quests'; $activeNav = 'pending'; require __DIR__ . '/_header.php'; ?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title">Pending Quests</h1>
        <p class="gm-subtitle">Review submitted contracts before they go up on the notice board.</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="gm-alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($quests)): ?>
    <div class="gm-quest-grid">
        <?php foreach ($quests as $quest): ?>
            <div class="gm-quest-card">
                <h3><?= htmlspecialchars($quest['title']) ?></h3>
                <p><?= htmlspecialchars($quest['description']) ?></p>

                <div class="gm-quest-meta">
                    <span class="gm-badge type"><?= htmlspecialchars(str_replace('_', ' ', $quest['type'] ?: 'unset')) ?></span>
                    <span class="gm-badge difficulty-<?= htmlspecialchars($quest['difficulty']) ?>"><?= htmlspecialchars($quest['difficulty']) ?></span>
                    <span class="gm-badge type">XP <?= (int) $quest['xp_reward'] ?></span>
                    <span class="gm-badge type">Coins <?= (int) $quest['coins_reward'] ?></span>
                </div>

                <?php if (!empty($quest['username'])): ?>
                    <p><strong>Submitted by:</strong> <?= htmlspecialchars($quest['username']) ?></p>
                <?php endif; ?>

                <?php if (!empty($quest['payment_proof'])): ?>
                    <div class="gm-quest-proof">
                        <a href="<?= ROOT ?>/uploads/payments/<?= htmlspecialchars($quest['payment_proof']) ?>" target="_blank">
                            <img src="<?= ROOT ?>/uploads/payments/<?= htmlspecialchars($quest['payment_proof']) ?>" alt="Payment proof">
                        </a>
                    </div>
                <?php else: ?>
                    <p><em>No payment proof uploaded.</em></p>
                <?php endif; ?>

                <div class="gm-quest-actions">
                    <a href="<?= ROOT ?>/admin/editQuest/<?= $quest['id'] ?>" class="gm-btn">Edit</a>
                    <a href="<?= ROOT ?>/admin/publishQuest/<?= $quest['id'] ?>" class="gm-btn gm-btn-primary">Publish</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="gm-empty">The notice board is clear. No pending quests.</div>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
