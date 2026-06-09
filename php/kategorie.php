<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo   = new RecipeRepository();
$categoryRepo = new CategoryRepository();
$favorites    = new Favorites();

$slug     = trim($_GET['slug'] ?? '');
$category = $slug !== '' ? $categoryRepo->getBySlug($slug) : null;

if ($category === null) {
    http_response_code(404);
    $pageTitle        = 'Kategorie nenalezena – Kottyho kuchařka';
    $favoritesCount   = $favorites->count();
    $nenalezenoNadpis = 'Kategorie nenalezena';
    $nenalezenoUrl    = 'kategorie-receptu.php';
    $nenalezenoText   = 'Zpět na kategorie →';
    require __DIR__ . '/views/nenalezeno.php';
    exit;
}

$recipes = $recipeRepo->getByCategorySlug($slug);

$pageTitle      = htmlspecialchars($category->name) . ' – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/kategorie.php';
