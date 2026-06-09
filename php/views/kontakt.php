<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="contact-page">
    <section class="contact-container">
        <h1>Kontakt</h1>

        <div class="contact-info" style="text-align:left;">
            <p><strong style="display:inline-block;min-width:90px;">Email:</strong> kottyho.kucharka@gmail.com</p>
            <p><strong style="display:inline-block;min-width:90px;">Telefon:</strong> <em style="color:#aaa;">připravujeme</em></p>
            <p><strong style="display:inline-block;min-width:90px;">Adresa:</strong> <em style="color:#aaa;">připravujeme</em></p>
        </div>

        <?php if ($success): ?>
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:16px 20px;margin-bottom:20px;color:#166534;font-weight:600;">
                ✓ Zpráva byla odeslána. Ozveme se co nejdříve!
            </div>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
            <div style="background:#fff0ee;border:1px solid #e26a2c;border-radius:10px;padding:16px 20px;margin-bottom:20px;">
                <?php foreach ($errors as $e): ?>
                    <p style="margin:0;color:#c0392b;"><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form class="contact-form" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <input type="text" name="jmeno" placeholder="Vaše jméno"
                        value="<?= htmlspecialchars($_POST['jmeno'] ?? '') ?>" required>
                    <input type="email" name="email" placeholder="Váš e-mail"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <textarea name="zprava" placeholder="Vaše zpráva" rows="5" required><?= htmlspecialchars($_POST['zprava'] ?? '') ?></textarea>
                <button type="submit" class="btn-submit">Odeslat zprávu</button>
            </form>
        <?php endif; ?>

    </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>