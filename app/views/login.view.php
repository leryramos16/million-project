<?php $title = "Login"; ?>
<?php require "../inc/header.php"; ?>

<?php
if (isset($_SESSION['user_id'])) {
  header('Location: /myapp/public/dashboard');
  exit;
}
?>

<body class="witcher-login-body">

<main class="witcher-login-wrapper">

  <form action="" method="post" class="witcher-login-card">

    <div class="login-emblem">
      <img src="<?=ROOT?>/assets/images/REALLIFEQUEST.png" alt="Adventurer">
    </div>

    <h1 class="login-title">Enter the Notice Board</h1>
    <p class="login-subtitle">Sign in, adventurer, and continue your quest.</p>

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

    <div class="quest-remember">
      <input type="checkbox" name="remember" id="remember">
      <label for="remember">Remember me</label>
    </div>

    <button class="quest-login-btn" type="submit">
      Begin Quest
    </button>

    <div class="quest-links">
      <a href="<?=ROOT?>/register">Create Adventurer</a>
      <span>|</span>
      <a href="<?= ROOT ?>/forgotpassword">Forgot Password?</a>
    </div>

    <p class="quest-footer">&copy; 2026 Notice Board</p>

  </form>

</main>

<?php require "../inc/footer.php"; ?>