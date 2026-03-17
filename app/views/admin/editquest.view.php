<!DOCTYPE html>
<html>
<head>
    <title>Edit Quest</title>
</head>
<body>

<h2>Edit Quest</h2>

<a href="<?= ROOT ?>/admin/viewPendingRequests">Back</a>

<form method="POST">

    <label>Title</label><br>
    <input type="text" name="title" 
        value="<?= htmlspecialchars($data['quest']['title']) ?>"><br><br>

    <label>Description</label><br>
    <textarea name="description"><?= htmlspecialchars($data['quest']['description']) ?></textarea><br><br>

    <label>XP Reward</label>
    <input type="number" name="xp_reward">

    <label>Coins Reward</label>
    <input type="number" name="coins_reward">

    <label>Type</label>
    <input type="text" name="type">
    <button type="submit">Update Quest</button>

</form>

</body>
</html>