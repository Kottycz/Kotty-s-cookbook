<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

// POST: toggle oblíbeného receptu – jen pro přihlášené
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorite'])) {
    csrf_verify();
    if (!Auth::isLoggedIn()) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
    $recipeId = (int) $_POST['recipe_id'];
    $recipe   = $recipeRepo->getById($recipeId);

    if ($recipe !== null) {
        $wasAdded = !$favorites->contains($recipe->id);
        $favorites->toggle($recipe->id);
        session_write_close();

        if ($wasAdded) {
            header('Location: oblibene-potvrzeni.php');
        } else {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    } else {
        session_write_close();
        header('Location: ' . $_SERVER['REQUEST_URI']);
    }
    exit;
}

$slug   = trim($_GET['slug'] ?? '');
$recipe = $slug !== '' ? $recipeRepo->getBySlug($slug) : null;

if ($recipe === null) {
    http_response_code(404);
    $pageTitle         = 'Recept nenalezen – Kottyho kuchařka';
    $favoritesCount    = $favorites->count();
    $nenalezenoNadpis  = 'Recept nenalezen';
    $nenalezenoUrl     = 'recepty.php';
    $nenalezenoText    = 'Zpět na recepty →';
    require __DIR__ . '/views/nenalezeno.php';
    exit;
}

// Soukromý recept smí zobrazit jen majitel nebo admin
if ($recipe->isPrivate()) {
    $cu = Auth::currentUser();
    if ($cu === null || ($cu->id !== $recipe->userId && !Auth::isAdmin())) {
        http_response_code(404);
        $pageTitle         = 'Recept nenalezen – Kottyho kuchařka';
        $favoritesCount    = $favorites->count();
        $nenalezenoNadpis  = 'Recept nenalezen';
        $nenalezenoUrl     = 'recepty.php';
        $nenalezenoText    = 'Zpět na recepty →';
        require __DIR__ . '/views/nenalezeno.php';
        exit;
    }
}

$ingredients    = $recipeRepo->getIngredients($recipe->id);
$steps          = $recipeRepo->getSteps($recipe->id);
$isFavorite     = $favorites->contains($recipe->id);
$pageTitle       = $recipe->name . ' – Kottyho kuchařka';
$pageDescription = $recipe->description !== '' ? mb_strimwidth($recipe->description, 0, 160, '…') : 'Recept ' . $recipe->name . ' na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();
$recipeFlash    = $_SESSION['recipe_flash'] ?? null;
unset($_SESSION['recipe_flash']);

require __DIR__ . '/views/recept.php';
