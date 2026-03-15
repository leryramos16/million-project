<a href="<?= ROOT ?>/logout">Logout</a>
<h2>Create New Quest</h2>

<?php 
// Show general error
if (!empty($data['error'])) {
    echo "<p style='color:red;'>{$data['error']}</p>";
}

// Show success message if set
if (isset($_SESSION['success_message'])) {
    echo "<p style='color:green;'>{$_SESSION['success_message']}</p>";
    unset($_SESSION['success_message']);
}
?>

<form method="POST" action="<?= ROOT ?>/quests/create">
    <label>Title:</label><br>
   <input type="text" name="title" value="<?= htmlspecialchars($data['title'] ?? '') ?>">
    
    <br><br>

    <label>Description:</label><br>
    <input type="text" name="description" value="<?= htmlspecialchars($data['description'] ?? '') ?>">
    
    <br><br>

    <label>XP Reward:</label><br>
    <input type="text" name="xp_reward" value="<?= htmlspecialchars($data['xp_reward'] ?? '') ?>"><br>

    <label>Coins Reward:</label><br>
    <input type="text" name="coins_reward" value="<?= htmlspecialchars($data['coins_reward'] ?? '') ?>"><br>

    <label>Type:</label><br>
    <select name="type">
        <option value="main_quests" <?= $data['type'] ?? '' === 'main_quests' ? 'selected' : '' ?>>Main</option>
        <option value="side_quests" <?= $data['type'] ?? '' === 'side_quests' ? 'selected' : '' ?>>Side</option>
        <option value="events" <?= $data['type'] ?? '' === 'events' ? 'selected' : '' ?>>Events</option>
    </select>
    <br><br>

    <button type="submit">Create Quest</button>
</form>