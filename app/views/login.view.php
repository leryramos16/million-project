<?php $title = "Login"; ?>
<?php require "../inc/header.php"; ?>

<?php
if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? null) === 'admin') {
  header('Location: ' . ROOT . '/admin');
  exit;
}
?>

<body class="witcher-login-body">

<main class="witcher-login-wrapper">

  <form action="" method="post" class="witcher-login-card">

    <div class="login-emblem">
      <img src="<?=ROOT?>/assets/images/REALLIFEQUEST.png" alt="Adventurer">
    </div>

    <h1 class="login-title">Game Master Console</h1>
    <p class="login-subtitle">Sign in to manage the notice board.</p>

    <?php if (!empty($error)): ?>
      <div class="quest-alert danger">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="quest-alert success">
        <?= htmlspecialchars($_SESSION['success']); ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="quest-field">
      <label for="usernameOrEmail">Username / Email</label>
      <input
        value="<?= htmlspecialchars($usernameOrEmail ?? '') ?>"
        name="usernameOrEmail"
        type="text"
        id="usernameOrEmail"
        placeholder="Enter your name or email"
      />
      <small><?= $usernameOrEmailErr ?></small>
    </div>

    <div class="quest-field">
      <label for="password">Password</label>
      <input
        name="password"
        type="password"
        id="password"
        placeholder="Enter your secret rune"
      />
      <small><?= $passwordErr ?></small>
    </div>

    <button class="quest-login-btn" type="submit">
      Enter the Console
    </button>

    <p class="quest-footer">Players: use the Quest companion app on Android.</p>
    <p class="quest-footer">&copy; 2026 Notice Board</p>

  </form>

</main>

<?php require "../inc/footer.php"; ?>