<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Auth::requireLogin();

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

$user = Auth::currentUser();
$slug = trim($_GET['slug'] ?? '');

$recipe = $slug !== '' ? $recipeRepo->getBySlug($slug) : null;

if ($recipe === null) {
    header('Location: moje-recepty.php');
    exit;
}

// Pouze majitel nebo admin může recept smazat
if ($recipe->userId !== $user->id && !Auth::isAdmin()) {
    header('Location: moje-recepty.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    csrf_verify();
    $recipeRepo->delete($recipe->id);
    $_SESSION['flash'] = 'Recept byl smazán.';
    header('Location: moje-recepty.php');
    exit;
}

$pageTitle       = 'Smazat: ' . $recipe->name . ' – Kottyho kuchařka';
$pageDescription = 'Smazat recept ' . $recipe->name . ' z vaší osobní knihovny na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/smazat-muj-recept.php';
