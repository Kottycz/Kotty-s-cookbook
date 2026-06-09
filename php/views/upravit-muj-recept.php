<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <div class="add-recipe-container">
        <h1>Upravit recept</h1>

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

        <form class="recipe-form" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <fieldset>
                <legend>Základní informace</legend>

                <label for="name">Název receptu *</label>
                <input type="text" id="name" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? $recipe->name) ?>" required>

                <label for="description">Popis</label>
                <textarea id="description" name="description"><?= htmlspecialchars($_POST['description'] ?? $recipe->description) ?></textarea>

                <label>Obrázek receptu</label>
                <div class="image-upload-group">
                    <label for="image_upload" class="btn-import">🖼 Nahrát nový obrázek</label>
                    <input type="file" id="image_upload" name="image_upload" accept="image/*" style="display:none">
                    <span id="image-upload-name" style="font-size:13px;color:#888;margin-left:10px;"></span>
                </div>
                <small>Nebo zadej cestu ručně:</small>
                <input type="text" id="image" name="image"
                       value="<?= htmlspecialchars($_POST['image'] ?? $recipe->image) ?>"
                       placeholder="assets/images/nazev.jpg">
                <small style="color:#888;">Aktuální: <?= htmlspecialchars($recipe->image) ?>. Max 5 MB (JPG, PNG, WebP, GIF).</small>

                <div class="form-two-col">
                    <div>
                        <label for="category_id">Kategorie *</label>
                        <select id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>"
                                    <?= (($_POST['category_id'] ?? $recipe->categoryId) == $cat->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="difficulty_id">Obtížnost *</label>
                        <select id="difficulty_id" name="difficulty_id" required>
                            <?php foreach ($difficulties as $diff): ?>
                                <option value="<?= $diff->id ?>"
                                    <?= (($_POST['difficulty_id'] ?? $recipe->difficultyId) == $diff->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($diff->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="prep_time">Příprava (min) *</label>
                        <input type="number" id="prep_time" name="prep_time" min="0" max="600"
                               value="<?= htmlspecialchars((string) ($_POST['prep_time'] ?? $recipe->prepTimeMinutes)) ?>" required>
                    </div>
                    <div>
                        <label for="cook_time">Vaření (min) *</label>
                        <input type="number" id="cook_time" name="cook_time" min="0" max="600"
                               value="<?= htmlspecialchars((string) ($_POST['cook_time'] ?? $recipe->cookTimeMinutes)) ?>" required>
                    </div>
                    <div>
                        <label for="servings">Porcí *</label>
                        <input type="number" id="servings" name="servings" min="1" max="100"
                               value="<?= htmlspecialchars((string) ($_POST['servings'] ?? $recipe->servings)) ?>" required>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Ingredience a postup</legend>

                <label for="ingredients">Ingredience <small>(každá na nový řádek)</small></label>
                <textarea id="ingredients" name="ingredients" rows="10"><?= htmlspecialchars($_POST['ingredients'] ?? $ingDefault) ?></textarea>

                <label for="steps">Postup přípravy <small>(každý krok na nový řádek)</small></label>
                <textarea id="steps" name="steps" rows="10"><?= htmlspecialchars($_POST['steps'] ?? $stepsDefault) ?></textarea>
            </fieldset>

            <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
                <button type="submit" class="btn-submit">Uložit změny</button>
                <a href="moje-recepty.php" class="btn-secondary" style="align-self:center;">← Zrušit</a>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
