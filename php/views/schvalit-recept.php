<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
<?php if ($success): ?>

    <div class="auth-form-wrapper" style="text-align:center;">
        <div style="font-size:3rem;margin-bottom:16px;">✅</div>
        <h1 style="color:#166534;">Recept zveřejněn!</h1>
        <p>Recept <strong><?= htmlspecialchars($recipe->name) ?></strong> byl úspěšně zveřejněn.</p>
        <a href="recept.php?slug=<?= urlencode($recipe->slug) ?>" class="btn-submit" style="text-decoration:none;display:inline-block;margin-top:20px;">
            Zobrazit recept →
        </a>
    </div>

<?php elseif ($rejected): ?>

    <div class="auth-form-wrapper" style="text-align:center;">
        <div style="font-size:3rem;margin-bottom:16px;">🚫</div>
        <h1 style="color:#be123c;">Recept zamítnut</h1>
        <p>Recept byl vrácen autorovi jako soukromý. Autor dostal e-mail s vysvětlením.</p>
        <a href="index.php" class="btn-secondary" style="display:inline-block;margin-top:20px;">← Zpět na hlavní stránku</a>
    </div>

<?php elseif ($error !== ''): ?>

    <div class="auth-form-wrapper" style="text-align:center;">
        <div style="font-size:3rem;margin-bottom:16px;">⚠️</div>
        <h1 style="color:#be123c;">Neplatný odkaz</h1>
        <p><?= htmlspecialchars($error) ?></p>
        <a href="index.php" class="btn-secondary" style="display:inline-block;margin-top:20px;">← Zpět na hlavní stránku</a>
    </div>

<?php else: ?>

    <!-- Náhled receptu před schválením -->
    <div style="max-width:860px;margin:0 auto;padding:0 20px;">

        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:12px;padding:16px 20px;margin-bottom:28px;display:flex;align-items:center;gap:12px;">
            <span style="font-size:1.4rem;">📋</span>
            <div>
                <strong>Náhled před schválením</strong>
                <p style="margin:4px 0 0;font-size:14px;color:#666;">Zkontroluj recept a schval nebo zamítni.</p>
            </div>
        </div>

        <h1><?= htmlspecialchars($recipe->name) ?></h1>

        <?php if ($recipe->description !== ''): ?>
            <p style="color:#555;font-size:1.05rem;margin-bottom:20px;"><?= htmlspecialchars($recipe->description) ?></p>
        <?php endif; ?>

        <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:24px;">
            <span>⏱ Příprava: <strong><?= $recipe->prepTimeMinutes ?> min</strong></span>
            <span>🍳 Vaření: <strong><?= $recipe->cookTimeMinutes ?> min</strong></span>
            <span>👥 Porcí: <strong><?= $recipe->servings ?></strong></span>
            <?php if ($recipe->difficultyName): ?>
                <span>⭐ <strong><?= htmlspecialchars($recipe->difficultyName) ?></strong></span>
            <?php endif; ?>
            <?php if ($recipe->categoryName): ?>
                <span>📂 <strong><?= htmlspecialchars($recipe->categoryName) ?></strong></span>
            <?php endif; ?>
        </div>

        <?php if ($recipe->image !== ''): ?>
            <img src="../<?= htmlspecialchars($recipe->image) ?>" alt="<?= htmlspecialchars($recipe->name) ?>"
                 style="width:100%;max-height:360px;object-fit:cover;border-radius:14px;margin-bottom:28px;">
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 2fr;gap:32px;margin-bottom:40px;">
            <div>
                <h2 style="font-size:1.2rem;margin-bottom:12px;">Ingredience</h2>
                <ul style="list-style:none;padding:0;margin:0;">
                    <?php foreach ($ingredients as $ing): ?>
                        <li style="padding:6px 0;border-bottom:1px solid #f0e8de;font-size:15px;">
                            <?= htmlspecialchars(trim($ing->getFormattedAmount() . ' ' . $ing->name)) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h2 style="font-size:1.2rem;margin-bottom:12px;">Postup</h2>
                <ol style="padding-left:20px;margin:0;">
                    <?php foreach ($steps as $step): ?>
                        <li style="padding:8px 0;border-bottom:1px solid #f0e8de;font-size:15px;line-height:1.5;">
                            <?= htmlspecialchars($step->description) ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

        <!-- Schválit -->
        <div style="padding:24px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;text-align:center;margin-bottom:16px;">
            <p style="margin:0 0 14px;font-weight:600;color:#166534;">Recept vypadá dobře?</p>
            <form method="post">
                <?= csrf_field() ?>
                <button type="submit" name="approve" value="1"
                        style="background:#16a34a;color:#fff;border:none;padding:14px 40px;border-radius:50px;font-size:1rem;font-weight:700;cursor:pointer;">
                    ✓ Schválit a zveřejnit
                </button>
            </form>
        </div>

        <!-- Zamítnout -->
        <div style="padding:24px;background:#fff1f2;border:1px solid #fecaca;border-radius:12px;">
            <p style="margin:0 0 14px;font-weight:600;color:#be123c;">Recept nechceš zveřejnit?</p>
            <form method="post">
                <?= csrf_field() ?>
                <textarea name="reason" rows="3" placeholder="Důvod zamítnutí pro autora (nepovinné)…"
                          style="width:100%;padding:10px 14px;border:1px solid #fecaca;border-radius:8px;font-size:14px;box-sizing:border-box;margin-bottom:12px;resize:vertical;"></textarea>
                <button type="submit" name="reject" value="1"
                        style="background:#be123c;color:#fff;border:none;padding:12px 32px;border-radius:50px;font-size:0.95rem;font-weight:700;cursor:pointer;">
                    ✕ Zamítnout recept
                </button>
            </form>
        </div>

    </div>

<?php endif; ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
