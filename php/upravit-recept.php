<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Auth::requireAdmin();

$recipeRepo   = new RecipeRepository();
$categoryRepo = new CategoryRepository();
$diffRepo     = new DifficultyRepository();
$favorites    = new Favorites();

$slug   = trim($_GET['slug'] ?? '');
$recipe = $slug !== '' ? $recipeRepo->getBySlug($slug) : null;

if ($recipe === null) {
    http_response_code(404);
    header('Location: recepty.php');
    exit;
}

$categories   = $categoryRepo->getAll();
$difficulties = $diffRepo->getAll();
$ingredients  = $recipeRepo->getIngredients($recipe->id);
$steps        = $recipeRepo->getSteps($recipe->id);

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image       = trim($_POST['image'] ?? '');
    $categoryId  = trim($_POST['category_id'] ?? '');
    $diffId      = trim($_POST['difficulty_id'] ?? '');
    $prepTime    = trim($_POST['prep_time'] ?? '');
    $cookTime    = trim($_POST['cook_time'] ?? '');
    $servings    = trim($_POST['servings'] ?? '');
    $ingText     = trim($_POST['ingredients'] ?? '');
    $stepsText   = trim($_POST['steps'] ?? '');

    // Zpracování nahraného obrázku
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image_upload'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Chyba při nahrávání obrázku (kód ' . $file['error'] . ').';
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize      = 5 * 1024 * 1024;
            $finfo        = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType     = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes, true)) {
                $errors[] = 'Povolené formáty obrázku: JPG, PNG, WebP, GIF.';
            } elseif ($file['size'] > $maxSize) {
                $errors[] = 'Obrázek je příliš velký (max 5 MB).';
            } else {
                $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $base     = preg_replace('/[^a-z0-9]+/', '-', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
                $base     = trim($base, '-') ?: 'recept';
                $filename = $base . '-' . time() . '.' . $ext;
                $dest     = __DIR__ . '/../assets/images/' . $filename;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $image = 'assets/images/' . $filename;
                } else {
                    $errors[] = 'Nepodařilo se uložit obrázek. Zkuste to znovu.';
                }
            }
        }
    }

    $allowedCat  = array_map(fn($c) => $c->id, $categories);
    $allowedDiff = array_map(fn($d) => $d->id, $difficulties);

    $v = new Validator();
    $v->required('name', $name, 'Název je povinný.')
      ->maxLength('name', $name, 200, 'Název nesmí být delší než 200 znaků.')
      ->required('category_id', $categoryId, 'Vyberte kategorii.')
      ->in('category_id', (int) $categoryId, $allowedCat, 'Neplatná kategorie.')
      ->required('difficulty_id', $diffId, 'Vyberte obtížnost.')
      ->in('difficulty_id', (int) $diffId, $allowedDiff, 'Neplatná obtížnost.')
      ->required('prep_time', $prepTime, 'Doba přípravy je povinná.')
      ->intRange('prep_time', $prepTime, 0, 600, 'Doba přípravy musí být 0–600 minut.')
      ->required('cook_time', $cookTime, 'Doba vaření je povinná.')
      ->intRange('cook_time', $cookTime, 0, 600, 'Doba vaření musí být 0–600 minut.')
      ->required('servings', $servings, 'Počet porcí je povinný.')
      ->intRange('servings', $servings, 1, 100, 'Počet porcí musí být 1–100.');

    $errors = array_merge($errors, $v->getErrors());

    if ($errors === []) {
        $recipeRepo->update(
            id:              $recipe->id,
            categoryId:      (int) $categoryId,
            difficultyId:    (int) $diffId,
            name:            $name,
            description:     $description,
            image:           $image !== '' ? $image : $recipe->image,
            prepTimeMinutes: (int) $prepTime,
            cookTimeMinutes: (int) $cookTime,
            servings:        (int) $servings,
        );

        $ingLines = array_values(array_filter(
            array_map('trim', explode("\n", $ingText)),
            fn($l) => $l !== '',
        ));
        $stepLines = array_values(array_filter(
            array_map('trim', explode("\n", $stepsText)),
            fn($l) => $l !== '',
        ));

        if ($ingLines !== []) {
            $recipeRepo->replaceIngredients($recipe->id, $ingLines);
        }
        if ($stepLines !== []) {
            $recipeRepo->replaceSteps($recipe->id, $stepLines);
        }

        header('Location: recept.php?slug=' . urlencode($recipe->slug));
        exit;
    }
}

$ingDefault   = implode("\n", array_map(fn($i) => trim($i->getFormattedAmount() . ' ' . $i->name), $ingredients));
$stepsDefault = implode("\n", array_map(fn($s) => $s->description, $steps));

$pageTitle       = 'Upravit: ' . $recipe->name . ' – Kottyho kuchařka';
$pageDescription = 'Upravte recept ' . $recipe->name . ' na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/upravit-recept.php';
