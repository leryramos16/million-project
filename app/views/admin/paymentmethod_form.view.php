<?php
$isEdit = isset($method);
$title = $isEdit ? 'Edit Payment Method' : 'Add Payment Method';
$activeNav = 'payments';
$m = $isEdit ? $method : ($old ?? []);
require __DIR__ . '/_header.php';
?>

<div class="gm-topbar">
    <div>
        <h1 class="gm-title"><?= $title ?></h1>
        <p class="gm-subtitle">This is shown to players when they submit a quest request.</p>
    </div>
</div>

<a href="<?= ROOT ?>/admin/paymentMethods" class="gm-btn" style="margin-bottom:20px; display:inline-block;">Back</a>

<?php if (!empty($errors)): ?>
    <div class="gm-alert danger">
        <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
    </div>
<?php endif; ?>

<div class="gm-form-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="gm-form-group">
            <label>Method</label>
            <select name="method">
                <?php foreach (['gcash' => 'GCash', 'maya' => 'Maya', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($m['method'] ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="gm-form-group">
            <label>Display label</label>
            <input type="text" name="label" placeholder="e.g. GCash" value="<?= htmlspecialchars($m['label'] ?? '') ?>" required>
        </div>

        <div class="gm-form-group">
            <label>Account name</label>
            <input type="text" name="account_name" placeholder="e.g. Juan Dela Cruz" value="<?= htmlspecialchars($m['account_name'] ?? '') ?>" required>
        </div>

        <div class="gm-form-group">
            <label>Account / mobile number</label>
            <input type="text" name="account_number" placeholder="e.g. 09171234567" value="<?= htmlspecialchars($m['account_number'] ?? '') ?>" required>
        </div>

        <div class="gm-form-group">
            <label>Instructions (optional)</label>
            <textarea name="instructions" rows="2" placeholder="e.g. Send exact amount, then upload the screenshot"><?= htmlspecialchars($m['instructions'] ?? '') ?></textarea>
        </div>

        <div class="gm-form-group">
            <label>QR code image (optional)</label>
            <?php if (!empty($m['qr_code_image'])): ?>
                <img src="<?= ROOT ?>/uploads/qrcodes/<?= htmlspecialchars($m['qr_code_image']) ?>" alt="Current QR" style="max-width:140px; display:block; margin-bottom:8px; border-radius:6px;">
            <?php endif; ?>
            <input type="file" name="qr_code" accept="image/png,image/jpeg">
        </div>

        <div class="gm-form-group">
            <label>
                <input type="checkbox" name="is_active" <?= ($m['is_active'] ?? 1) ? 'checked' : '' ?> style="width:auto;">
                Visible to players
            </label>
        </div>

        <button type="submit" class="gm-btn gm-btn-primary"><?= $isEdit ? 'Update' : 'Add' ?> Payment Method</button>
    </form>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
