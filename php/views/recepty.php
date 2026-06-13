<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <section class="category-header">
        <h1>Všechny recepty</h1>
        <p>Najděte si svůj oblíbený pokrm.</p>
    </section>

    <?php
        $diffLabel  = 'Obtížnost';
        foreach ($difficulties as $d) { if ($d->id === $difficultyId) { $diffLabel = $d->name; break; } }
        $timeLabels = ['do30' => 'Do 30 minut', 'do60' => 'Do 60 minut', 'nad60' => 'Nad 60 minut'];
        $timeLabel  = $timeLabels[$time] ?? 'Čas přípravy';
        $curDiff    = $difficultyId ?? '';
        $curTime    = $time ?? '';
    ?>
    <div class="recipes-filter">

        <div class="fdd">
            <button class="fdd__btn" type="button">
                <?= htmlspecialchars($diffLabel) ?>
                <svg class="fdd__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <ul class="fdd__menu">
                <li><a class="fdd__item <?= $difficultyId === null ? 'fdd__item--active' : '' ?>" href="recepty.php<?= $curTime ? '?time=' . urlencode($curTime) : '' ?>">Všechny obtížnosti</a></li>
                <?php foreach ($difficulties as $d): ?>
                <li><a class="fdd__item <?= $difficultyId === $d->id ? 'fdd__item--active' : '' ?>" href="recepty.php?difficulty=<?= $d->id ?><?= $curTime ? '&time=' . urlencode($curTime) : '' ?>"><?= htmlspecialchars($d->name) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="fdd">
            <button class="fdd__btn" type="button">
                <?= htmlspecialchars($timeLabel) ?>
                <svg class="fdd__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <ul class="fdd__menu">
                <li><a class="fdd__item <?= $time === null    ? 'fdd__item--active' : '' ?>" href="recepty.php<?= $curDiff ? '?difficulty=' . $curDiff : '' ?>">Jakýkoliv čas</a></li>
                <li><a class="fdd__item <?= $time === 'do30'  ? 'fdd__item--active' : '' ?>" href="recepty.php?time=do30<?= $curDiff  ? '&difficulty=' . $curDiff : '' ?>">Do 30 minut</a></li>
                <li><a class="fdd__item <?= $time === 'do60'  ? 'fdd__item--active' : '' ?>" href="recepty.php?time=do60<?= $curDiff  ? '&difficulty=' . $curDiff : '' ?>">Do 60 minut</a></li>
                <li><a class="fdd__item <?= $time === 'nad60' ? 'fdd__item--active' : '' ?>" href="recepty.php?time=nad60<?= $curDiff ? '&difficulty=' . $curDiff : '' ?>">Nad 60 minut</a></li>
            </ul>
        </div>

        <?php if ($difficultyId !== null || $time !== null): ?>
            <a href="recepty.php" class="filter-reset">✕ Zrušit</a>
        <?php endif; ?>
    </div>

    <script>
    document.querySelectorAll('.fdd').forEach(function(fdd) {
        fdd.querySelector('.fdd__btn').addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.fdd.open').forEach(function(o) { if (o !== fdd) o.classList.remove('open'); });
            fdd.classList.toggle('open');
        });
    });
    document.addEventListener('click', function() {
        document.querySelectorAll('.fdd.open').forEach(function(o) { o.classList.remove('open'); });
    });
    </script>

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
            <p>Zatím tu nejsou žádné recepty.</p>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
