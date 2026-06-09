<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <div class="add-recipe-container">
        <h1>Přidat nový recept</h1>
        <?php if (!Auth::isAdmin()): ?>
            <p style="color:#888;margin-top:-4px;margin-bottom:20px;font-size:14px;">Recept bude zveřejněn po schválení administrátorem.</p>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
            <div class="form-errors" style="background:#fff0ee;border:1px solid #e26a2c;border-radius:8px;padding:16px;margin-bottom:24px;">
                <p style="margin:0 0 8px;font-weight:600;">Opravte prosím chyby:</p>
                <ul style="margin:0;padding-left:20px;">
                    <?php foreach ($errors as $msg): ?>
                        <li><?= htmlspecialchars($msg) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="import-txt-box">
            <p>Máš recept v textovém souboru? Nahraj ho a formulář se vyplní automaticky.</p>
            <label for="txt-import" class="btn-import">📄 Načíst z .txt souboru</label>
            <input type="file" id="txt-import" accept=".txt" style="display:none">
            <span id="import-status"></span>
            <details style="margin-top:12px;">
                <summary style="cursor:pointer;font-size:13px;color:#888;">Jak má vypadat soubor?</summary>
                <pre class="import-example">Název: Svíčková na smetaně
Popis: Tradiční český pokrm
Příprava: 30
Vaření: 90
Porce: 4

Ingredience:
800 g hovězí svíčková
200 ml smetana
2 ks mrkve

Postup:
Maso omyjeme a nakrájíme.
Orestujeme na oleji dozlatova.
Dusíme 90 minut.</pre>
            </details>
        </div>

        <form class="recipe-form" method="post" id="recipe-form" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <fieldset>
                <legend>Základní informace</legend>

                <label for="name">Název receptu *</label>
                <input type="text" id="name" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       placeholder="Název receptu" required>

                <label for="description">Popis</label>
                <textarea id="description" name="description" placeholder="Krátký popis receptu"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                <label>Obrázek receptu</label>
                <div class="image-upload-group">
                    <label for="image_upload" class="btn-import">🖼 Nahrát obrázek ze zařízení</label>
                    <input type="file" id="image_upload" name="image_upload" accept="image/*" style="display:none">
                    <span id="image-upload-name" style="font-size:13px;color:#888;margin-left:10px;"></span>
                </div>
                <small>Nebo zadej cestu ručně:</small>
                <input type="text" id="image" name="image"
                       value="<?= htmlspecialchars($_POST['image'] ?? '') ?>"
                       placeholder="assets/images/nazev.jpg">
                <small>Nechej obojí prázdné pro výchozí obrázek. Max velikost souboru: 5 MB (JPG, PNG, WebP, GIF).</small>

                <div class="form-two-col">
                    <div>
                        <label for="category_id">Kategorie *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">– Vyberte kategorii –</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>"
                                    <?= (($_POST['category_id'] ?? '') == $cat->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="difficulty_id">Obtížnost *</label>
                        <select id="difficulty_id" name="difficulty_id" required>
                            <option value="">– Vyberte obtížnost –</option>
                            <?php foreach ($difficulties as $diff): ?>
                                <option value="<?= $diff->id ?>"
                                    <?= (($_POST['difficulty_id'] ?? '') == $diff->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($diff->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="prep_time">Příprava (min) *</label>
                        <input type="number" id="prep_time" name="prep_time" min="1" max="600"
                               value="<?= htmlspecialchars($_POST['prep_time'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="cook_time">Vaření (min) *</label>
                        <input type="number" id="cook_time" name="cook_time" min="0" max="600"
                               value="<?= htmlspecialchars($_POST['cook_time'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="servings">Porcí *</label>
                        <input type="number" id="servings" name="servings" min="1" max="100"
                               value="<?= htmlspecialchars($_POST['servings'] ?? '') ?>" required>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Ingredience a postup</legend>

                <label for="ingredients">Ingredience * <small>(každou na nový řádek)</small></label>
                <textarea id="ingredients" name="ingredients" rows="8"
                          placeholder="200 g mouky&#10;3 vejce&#10;100 ml mléka" required><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>

                <label for="steps">Postup přípravy * <small>(každý krok na nový řádek)</small></label>
                <textarea id="steps" name="steps" rows="8"
                          placeholder="Smíchejte mouku s vejci.&#10;Přidejte mléko a promíchejte.&#10;Smažte na pánvi." required><?= htmlspecialchars($_POST['steps'] ?? '') ?></textarea>
            </fieldset>

            <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
                <button type="submit" class="btn-submit">Přidat recept</button>
                <a href="recepty.php" class="btn-secondary" style="align-self:center;">← Zrušit</a>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
