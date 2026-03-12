<?php $title = "Login"; ?>
<?php require "../inc/header.php"; ?>

  <?php

if (isset($_SESSION['user_id'])) {
  header('Location: /myapp/public/dashboard');
  exit;
}

?>
  <body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
      <form action="" method="post">
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success text-center">
          <?= htmlspecialchars($_SESSION['success']); ?>
      </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

        <img
          class="mb-4"
          src="<?=ROOT?>/assets/images/creator.jpg"
          alt=""
          width="72"
          height="72"
        />
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
        <div class="form-floating">
          <input
            value="<?= htmlspecialchars($usernameOrEmail ?? '') ?>"
            name="usernameOrEmail"
            type="text"
            class="form-control"
            id="usernameOrEmail"
            placeholder="name@example.com"
          />
          <small class="text-danger"><?= $usernameOrEmailErr ?></small>
          <label for="floatingInput">Username/Email</label>
          
        </div>
        <div class="form-floating">
          <input
            name="password"
            type="password"
            class="form-control"
            id="password"
            placeholder="Password"
          />
          <label for="floatingPassword">Password</label>
          <small class="text-danger"><?= $passwordErr ?></small>
        </div>
        <div class="form-check text-start my-3">
          <input
            class="form-check-input"
            type="checkbox"
            name="remember"
            id="remember"
          />
          <label class="form-check-label">
            Remember me
          </label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit">
          Sign in
        </button>
        
        <a href="<?=ROOT?>/register">Signup</a>
        <div class="text-center mt-3">
          <a href="<?= ROOT ?>/forgotpassword">Forgot your password?</a>
        </div>
        <p class="mt-5 mb-3 text-body-secondary">&copy; 2026</p>
      </form>
    </main>
    
    <?php require "../inc/footer.php"; ?>
