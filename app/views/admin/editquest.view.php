<?php $title = 'Edit Quest'; $activeNav = 'pending'; require __DIR__ . '/_header.php'; ?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title">Edit Quest</h1>
        <p class="gm-subtitle">Adjust the contract before publishing it to the notice board.</p>
    </div>
</div>

<a href="<?= ROOT ?>/admin/viewPendingRequests" class="gm-btn" style="margin-bottom:20px; display:inline-block;">Back</a>

<div class="gm-form-card">
    <form method="POST">
        <div class="gm-form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($quest['title']) ?>" required>
        </div>

        <div class="gm-form-group">
            <label>Description</label>
            <textarea name="description" rows="4" required><?= htmlspecialchars($quest['description']) ?></textarea>
        </div>

        <div class="gm-form-group">
            <label>XP Reward</label>
            <input type="number" name="xp_reward" value="<?= (int) $quest['xp_reward'] ?>" min="0">
        </div>

        <div class="gm-form-group">
            <label>Coins Reward</label>
            <input type="number" name="coins_reward" value="<?= (int) $quest['coins_reward'] ?>" min="0">
        </div>

        <div class="gm-form-group">
            <label>Type</label>
            <select name="type">
                <?php foreach (['main_quests' => 'Main', 'side_quests' => 'Side', 'events' => 'Events'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $quest['type'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="gm-form-group">
            <label>Difficulty</label>
            <select name="difficulty">
                <?php foreach (['easy', 'medium', 'hard', 'legendary'] as $value): ?>
                    <option value="<?= $value ?>" <?= ($quest['difficulty'] ?? 'easy') === $value ? 'selected' : '' ?>><?= ucfirst($value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="gm-btn gm-btn-primary">Update Quest</button>
    </form>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
