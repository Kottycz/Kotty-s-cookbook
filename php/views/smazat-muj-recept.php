<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <div class="auth-form-wrapper">
        <h1 style="color:#be123c;">Smazat recept</h1>
        <p>Opravdu chceš smazat recept <strong><?= htmlspecialchars($recipe->name) ?></strong>? Tato akce je nevratná.</p>

        <form method="post">
            <?= csrf_field() ?>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" name="confirm_delete" value="1"
                        style="background:#be123c;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">
                    Ano, smazat
                </button>
                <a href="moje-recepty.php" class="btn-secondary">← Zpět</a>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
