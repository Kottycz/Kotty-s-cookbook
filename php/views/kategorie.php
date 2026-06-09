<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <section class="category-header">
        <h1><?= htmlspecialchars($category->name) ?></h1>
        <?php if ($category->description !== ''): ?>
            <p><?= htmlspecialchars($category->description) ?></p>
        <?php endif; ?>
    </section>

    <section class="recipes-grid">
        <?php foreach ($recipes as $recipe): ?>
            <a href="recept.php?slug=<?= urlencode($recipe->slug) ?>" class="recipe-card-link">
                <article class="recipe-card">
                    <img src="../<?= htmlspecialchars($recipe->image) ?>" alt="<?= htmlspecialchars($recipe->name) ?>">
                    <div class="recipe-content">
                        <h3><?= htmlspecialchars($recipe->name) ?></h3>
                        <p><?= htmlspecialchars($recipe->description) ?></p>
                        <div class="recipe-meta">
                            <span>⏱ <?= htmlspecialchars($recipe->getFormattedTotalTime()) ?></span>
                            <span>👥 <?= $recipe->servings ?> porcí</span>
                            <span>⭐ <?= htmlspecialchars($recipe->difficultyName ?? '') ?></span>
                        </div>
                    </div>
                </article>
            </a>
        <?php endforeach; ?>

        <?php if ($recipes === []): ?>
            <p>V této kategorii zatím nejsou žádné recepty.</p>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
