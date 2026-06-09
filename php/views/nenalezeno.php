<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <section class="category-header">
        <h1><?= htmlspecialchars($nenalezenoNadpis) ?></h1>
        <p><a href="<?= htmlspecialchars($nenalezenoUrl) ?>"><?= htmlspecialchars($nenalezenoText) ?></a></p>
    </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
