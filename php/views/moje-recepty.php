<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <div style="max-width:1200px;margin:0 auto;padding:0 20px;">

        <section class="category-header" style="text-align:center;">
            <h1>Moje recepty</h1>
            <p>Tvoje soukromá kuchařka.</p>
            <a href="pridat-muj-recept.php" class="btn-submit" style="text-decoration:none;display:inline-block;margin-top:12px;">+ Přidat recept</a>
        </section>

        <?php if ($flash !== null): ?>
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:14px 18px;margin-bottom:24px;color:#166534;">
                <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <?php if ($myRecipes === []): ?>
            <div style="text-align:center;padding:60px 20px;color:#888;">
                <p style="font-size:1.1rem;">Zatím nemáš žádné recepty.</p>
                <a href="pridat-muj-recept.php" class="btn-submit" style="text-decoration:none;display:inline-block;margin-top:16px;">Přidat první recept</a>
            </div>
        <?php else: ?>
            <div class="recipes-grid">
                <?php foreach ($myRecipes as $r): ?>
                    <div class="my-recipe-item">

                        <a href="recept.php?slug=<?= urlencode($r->slug) ?>" class="recipe-card-link"><?php /* vždy klikatelné – soukromé recept vidí jen majitel */ ?>

                            <article class="recipe-card">
                                <div class="my-recipe-img-wrap">
                                    <img src="../<?= htmlspecialchars($r->image) ?>" alt="<?= htmlspecialchars($r->name) ?>">
                                    <?php if ($r->visibility === 'public' && $r->status === 'approved'): ?>
                                        <span class="my-recipe-badge my-recipe-badge--public">✓ Zveřejněný</span>
                                    <?php elseif ($r->status === 'pending'): ?>
                                        <span class="my-recipe-badge my-recipe-badge--pending">⏳ Čeká</span>
                                    <?php else: ?>
                                        <span class="my-recipe-badge my-recipe-badge--private">🔒 Soukromý</span>
                                    <?php endif; ?>
                                </div>
                                <div class="recipe-content">
                                    <h3><?= htmlspecialchars($r->name) ?></h3>
                                    <p><?= htmlspecialchars($r->description) ?></p>
                                    <div class="recipe-meta">
                                        <span>⏱ <?= htmlspecialchars($r->getFormattedTotalTime()) ?></span>
                                        <span>👥 <?= $r->servings ?> porcí</span>
                                        <span>⭐ <?= htmlspecialchars($r->difficultyName ?? '') ?></span>
                                    </div>
                                </div>
                            </article>

                        </a>


                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
