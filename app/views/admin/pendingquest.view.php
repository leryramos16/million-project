<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Quest Requests</title>
</head>
<body>
    <?php if(isset($_SESSION['success'])): ?>

        <p style="color:green;">
            <?= $_SESSION['success']; ?>
        </p>

    <?php unset($_SESSION['success']); ?>

    <?php endif; ?>
    <h1 class="board-title">Pending Quest Requests</h1>
    <a href="<?= ROOT ?>/admin">Back</a>
    
<div class="notice-board" style="max-width:800px; margin:50px auto; padding:30px;">
    <?php if(!empty($data['quests'])): ?>
        <?php foreach($data['quests'] as $quest): ?>
            <div class="quest-paper" style="margin-bottom:20px; padding:20px;">
                <h3><?= htmlspecialchars($quest['title']) ?></h3>
                <!--<p><strong>Submitted by:</strong> <?= htmlspecialchars($quest['username']) ?></p> -->
                <p><?= htmlspecialchars($quest['description']) ?></p>

                <?php if(!empty($quest['payment_proof'])): ?>
                    <p><strong>Payment Proof:</strong></p>

                        <a href="<?= ROOT ?>/public/uploads/payments/<?= $quest['payment_proof'] ?>" target="_blank">
                            <img src="<?= ROOT ?>/public/uploads/payments/<?= $quest['payment_proof'] ?>" width="200">
                        </a>
                <?php else: ?>
                    <p>No payment proof uploaded.</p>
                <?php endif; ?>

                <a href="<?= ROOT ?>/admin/editQuest/<?= $quest['id'] ?>" style="margin-right:10px;">Edit</a>
                <a href="<?= ROOT ?>/admin/publishQuest/<?= $quest['id'] ?>">Publish</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No pending quests.</p>
    <?php endif; ?>
</div>
</body>
</html>