<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Auth::requireLogin();

$recipeRepo     = new RecipeRepository();
$categoryRepo   = new CategoryRepository();
$difficultyRepo = new DifficultyRepository();
$favorites      = new Favorites();

$categories   = $categoryRepo->getAll();
$difficulties = $difficultyRepo->getAll();
$errors       = [];

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
    $ingredients = trim($_POST['ingredients'] ?? '');
    $steps       = trim($_POST['steps'] ?? '');

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

    $allowedCategoryIds   = array_map(fn($c) => $c->id, $categories);
    $allowedDifficultyIds = array_map(fn($d) => $d->id, $difficulties);

    $v = new Validator();
    $v->required('name', $name, 'Název receptu je povinný.')
      ->maxLength('name', $name, 200, 'Název nesmí být delší než 200 znaků.')
      ->required('category_id', $categoryId, 'Vyberte kategorii.')
      ->in('category_id', (int) $categoryId, $allowedCategoryIds, 'Neplatná kategorie.')
      ->required('difficulty_id', $diffId, 'Vyberte obtížnost.')
      ->in('difficulty_id', (int) $diffId, $allowedDifficultyIds, 'Neplatná obtížnost.')
      ->required('prep_time', $prepTime, 'Doba přípravy je povinná.')
      ->intRange('prep_time', $prepTime, 1, 600, 'Doba přípravy musí být 1–600 minut.')
      ->required('cook_time', $cookTime, 'Doba vaření je povinná.')
      ->intRange('cook_time', $cookTime, 0, 600, 'Doba vaření musí být 0–600 minut.')
      ->required('servings', $servings, 'Počet porcí je povinný.')
      ->intRange('servings', $servings, 1, 100, 'Počet porcí musí být 1–100.')
      ->required('ingredients', $ingredients, 'Zadejte alespoň jednu ingredienci.')
      ->required('steps', $steps, 'Zadejte alespoň jeden krok postupu.');

    $errors = array_merge($errors, $v->getErrors());

    if ($errors === []) {
        $user = Auth::currentUser();

        $ingredientLines = array_values(array_filter(
            array_map('trim', explode("\n", $ingredients)),
            fn($l) => $l !== '',
        ));
        $stepLines = array_values(array_filter(
            array_map('trim', explode("\n", $steps)),
            fn($l) => $l !== '',
        ));

        // Vytvoří soukromý recept přiřazený uživateli
        $slug = $recipeRepo->create(
            categoryId:      (int) $categoryId,
            difficultyId:    (int) $diffId,
            name:            $name,
            description:     $description,
            image:           $image !== '' ? $image : 'assets/images/hlavni-jidla.jpeg',
            prepTimeMinutes: (int) $prepTime,
            cookTimeMinutes: (int) $cookTime,
            servings:        (int) $servings,
            userId:          $user->id,
            visibility:      'private',
            status:          'approved',
        );

        $newRecipe = $recipeRepo->getBySlug($slug);
        $recipeRepo->replaceIngredients($newRecipe->id, $ingredientLines);
        $recipeRepo->replaceSteps($newRecipe->id, $stepLines);

        $_SESSION['flash'] = 'Recept byl uložen do tvé knihovny.';
        header('Location: moje-recepty.php');
        exit;
    }
}

$pageTitle       = 'Přidat recept do knihovny – Kottyho kuchařka';
$pageDescription = 'Přidejte vlastní recept do vaší osobní knihovny receptů na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/pridat-muj-recept.php';
