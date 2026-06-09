<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

$recipes = $recipeRepo->getAll();

$pageTitle      = 'Recepty – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/recepty.php';
