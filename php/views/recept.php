<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="recipe-page">
    <article class="recipe-container">

        <header class="recipe-header">
            <h1><?= htmlspecialchars($recipe->name) ?></h1>
            <div class="recipe-image-wrapper">
                <img src="../<?= htmlspecialchars($recipe->image) ?>" alt="<?= htmlspecialchars($recipe->name) ?>">
            </div>
        </header>

        <div class="recipe-meta-strip">
            <div class="meta-item">
                <span>⏱</span>
                <p><?= htmlspecialchars($recipe->getFormattedTotalTime()) ?></p>
            </div>
            <div class="meta-item">
                <span>👥</span>
                <p><?= $recipe->servings ?> porcí</p>
            </div>
            <div class="meta-item">
                <span>⭐</span>
                <p><?= htmlspecialchars($recipe->difficultyName ?? '') ?></p>
            </div>
            <div class="meta-item">
                <span>🍽</span>
                <p><?= htmlspecialchars($recipe->categoryName ?? '') ?></p>
            </div>
        </div>

        <?php if ($recipe->description !== ''): ?>
            <p style="text-align:center; color:#555; font-size:1.05rem; max-width:700px; margin: 0 auto 40px auto; line-height:1.7;">
                <?= htmlspecialchars($recipe->description) ?>
            </p>
        <?php endif; ?>

        <div class="recipe-content-grid">
            <section class="ingredients">
                <h2>Ingredience</h2>
                <?php if ($ingredients === []): ?>
                    <p>Recept nemá zadané suroviny.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($ingredients as $ing): ?>
                            <li>
                                <?php if ($ing->getFormattedAmount() !== ''): ?>
                                    <strong><?= htmlspecialchars($ing->getFormattedAmount()) ?></strong>
                                <?php endif; ?>
                                <?= htmlspecialchars($ing->name) ?>
                                <?php if ($ing->note !== ''): ?>
                                    <em>(<?= htmlspecialchars($ing->note) ?>)</em>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="instructions">
                <h2>Postup přípravy</h2>
                <?php if ($steps === []): ?>
                    <p>Recept zatím nemá zadaný postup.</p>
                <?php else: ?>
                    <ol>
                        <?php foreach ($steps as $step): ?>
                            <li><?= htmlspecialchars($step->description) ?></li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>
        </div>

        <?php
            $currentUser = Auth::currentUser();
            $isOwner     = $currentUser !== null && $recipe->userId === $currentUser->id && !Auth::isAdmin();
        ?>

        <?php if ($recipeFlash !== null): ?>
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 18px;margin-bottom:16px;text-align:center;font-size:14px;color:#166534;">
                ✓ <?= htmlspecialchars($recipeFlash) ?>
            </div>
        <?php endif; ?>

        <?php if ($isOwner && $recipe->isPrivate()): ?>
            <div style="background:<?= $recipe->isPending() ? '#fef9c3' : '#f1f5f9' ?>;border:1px solid <?= $recipe->isPending() ? '#fde68a' : '#e2e8f0' ?>;border-radius:10px;padding:12px 18px;margin-bottom:24px;text-align:center;font-size:14px;color:#555;">
                <?php if ($recipe->isPending()): ?>
                    ⏳ Tento recept <strong>čeká na schválení</strong> administrátorem.
                <?php else: ?>
                    🔒 Tento recept je <strong>soukromý</strong> — vidíš ho pouze ty.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="recipe-actions">
            <?php if (Auth::isLoggedIn()): ?>
                <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="recipe_id" value="<?= $recipe->id ?>">
                    <button type="submit" name="toggle_favorite"
                        style="background:<?= $isFavorite ? '#888' : '#e26a2c' ?>;color:#fff;border:none;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;cursor:pointer;transition:background 0.3s;">
                        <?= $isFavorite ? '♥ Odebrat z oblíbených' : '♡ Přidat do oblíbených' ?>
                    </button>
                </form>
            <?php else: ?>
                <button type="button"
                    onclick="document.getElementById('login-modal').classList.add('open')"
                    style="background:#e26a2c;color:#fff;border:none;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;cursor:pointer;">
                    ♡ Přidat do oblíbených
                </button>
            <?php endif; ?>

            <?php if (Auth::isAdmin()): ?>
                <?php if ($recipe->isPrivate() || $recipe->isPending()): ?>
                    <form method="post" action="zverejnit-recept.php" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="recipe_id" value="<?= $recipe->id ?>">
                        <button type="submit"
                                style="background:#16a34a;color:#fff;border:none;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;cursor:pointer;">
                            ✓ Zveřejnit
                        </button>
                    </form>
                <?php endif; ?>
                <a href="upravit-recept.php?slug=<?= urlencode($recipe->slug) ?>"
                   style="background:#444;color:#fff;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;text-decoration:none;">
                    ✏ Upravit recept
                </a>
                <a href="smazat-recept.php?slug=<?= urlencode($recipe->slug) ?>"
                   style="background:#d32f2f;color:#fff;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;text-decoration:none;">
                    🗑 Smazat recept
                </a>
            <?php elseif ($isOwner): ?>
                <a href="upravit-muj-recept.php?slug=<?= urlencode($recipe->slug) ?>"
                   style="background:#444;color:#fff;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;text-decoration:none;">
                    ✏ Upravit
                </a>
                <a href="smazat-muj-recept.php?slug=<?= urlencode($recipe->slug) ?>"
                   style="background:#d32f2f;color:#fff;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;text-decoration:none;">
                    🗑 Smazat
                </a>
                <?php if ($recipe->isPrivate() && !$recipe->isPending()): ?>
                    <form method="post" action="zverejnit-recept.php" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="recipe_id" value="<?= $recipe->id ?>">
                        <button type="submit"
                                style="background:#e26a2c;color:#fff;border:none;padding:16px 35px;border-radius:50px;font-size:1rem;font-weight:600;cursor:pointer;">
                            📤 Zveřejnit
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <a href="<?= $recipe->isPrivate() ? 'moje-recepty.php' : 'recepty.php' ?>" class="btn-secondary">
                ← <?= $recipe->isPrivate() ? 'Zpět na Moje recepty' : 'Zpět na recepty' ?>
            </a>
        </div>

    </article>
</main>

<!-- Popup pro nepřihlášené – oblíbené -->
<div id="login-modal" class="login-modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="login-modal-box">
        <p style="font-size:2rem;margin:0 0 8px;">🔒</p>
        <h2 style="margin:0 0 10px;">Přihlaš se</h2>
        <p style="color:#666;margin:0 0 24px;">Pro přidání do oblíbených se musíš přihlásit nebo registrovat.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="prihlasit.php" class="btn-submit" style="text-decoration:none;">Přihlásit se</a>
            <a href="registrovat.php" class="btn-secondary">Registrovat se</a>
        </div>
        <button onclick="document.getElementById('login-modal').classList.remove('open')"
                style="margin-top:18px;background:none;border:none;color:#aaa;cursor:pointer;font-size:13px;">Zavřít</button>
    </div>
</div>

<script>
(function () {
    if (!('wakeLock' in navigator)) return;

    let lock = null;

    async function acquire() {
        try {
            lock = await navigator.wakeLock.request('screen');
        } catch (_) {}
    }

    // Znovu zamkni displej pokud se uživatel vrátí na stránku
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') acquire();
    });

    acquire();
})();
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
