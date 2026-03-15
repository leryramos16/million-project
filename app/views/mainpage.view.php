<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet">
    <link href="<?= ROOT ?>/assets/css/quest.css" rel="stylesheet">
</head>
<body>
    <a class="logout-btn" href="<?= ROOT ?>/logout">Logout</a>

<h1 class="board-title">Notice Board</h1>

<div class="notice-board">
    <div class="nail-bottom-left"></div>
    <div class="nail-bottom-right"></div>
<?php if(!empty($data['quests'])): ?>

<?php foreach($data['quests'] as $quest): ?>

<div class="quest-paper">

<h3><?= htmlspecialchars($quest['title']) ?></h3>

<p><?= htmlspecialchars($quest['description']) ?></p>

<p><strong>XP:</strong> <?= $quest['xp_reward'] ?></p>

<p><strong>Coins:</strong> <?= $quest['coins_reward'] ?></p>

<p><strong>Type:</strong> <?= $quest['type'] ?></p>

</div>

<?php endforeach; ?>

<?php else: ?>

<p>No quests available.</p>

<?php endif; ?>

</div>
</body>
</html>