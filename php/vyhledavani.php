<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

$query   = trim($_GET['q'] ?? '');
$recipes = $query !== '' ? $recipeRepo->search($query) : [];

$pageTitle      = 'Vyhledávání – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/vyhledavani.php';
