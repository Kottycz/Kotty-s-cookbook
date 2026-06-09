<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <div class="auth-form-wrapper">
        <h1>Registrace</h1>

        <?php if ($errors !== []): ?>
            <div class="form-errors">
                <?php foreach ($errors as $msg): ?>
                    <p><?= htmlspecialchars($msg) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Heslo <small>(min. 6 znaků)</small></label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password2">Heslo znovu</label>
                <input type="password" id="password2" name="password2" required>
            </div>

            <?php if (RECAPTCHA_SITE_KEY !== ''): ?>
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY) ?>" style="margin-bottom:16px;"></div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Zaregistrovat se</button>
        </form>

        <p class="auth-switch">Už máš účet? <a href="prihlasit.php">Přihlás se</a></p>
    </div>
</main>

<?php if (RECAPTCHA_SITE_KEY !== ''): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>