<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

$slug   = trim($_GET['slug'] ?? '');
$recipe = $slug !== '' ? $recipeRepo->getBySlug($slug) : null;

if ($recipe === null) {
    header('Location: recepty.php');
    exit;
}

$pageTitle      = 'Recept přidán – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/recept-pridan.php';
