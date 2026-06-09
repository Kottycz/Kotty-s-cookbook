<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <div class="auth-form-wrapper">
        <h1>Přihlásit se</h1>

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
                <label for="password">Heslo</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">Přihlásit se</button>
        </form>

        <p class="auth-switch">Nemáš účet? <a href="registrovat.php">Zaregistruj se</a></p>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
