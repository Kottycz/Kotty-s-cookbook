<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Auth::requireAdmin();

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

$slug   = trim($_GET['slug'] ?? '');
$recipe = $slug !== '' ? $recipeRepo->getBySlug($slug) : null;

if ($recipe === null) {
    http_response_code(404);
    header('Location: recepty.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    csrf_verify();
    $recipeRepo->delete($recipe->id);
    header('Location: recepty.php');
    exit;
}

$pageTitle       = 'Smazat: ' . $recipe->name . ' – Kottyho kuchařka';
$pageDescription = 'Smazat recept ' . $recipe->name . ' z Kottyho kuchařky.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/smazat-recept.php';
