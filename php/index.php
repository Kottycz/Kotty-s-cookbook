<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo   = new RecipeRepository();
$categoryRepo = new CategoryRepository();
$favorites    = new Favorites();

$featured   = $recipeRepo->getFeaturedHourly(6);
$categories = $categoryRepo->getAll();

$pageTitle      = 'Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/index.php';
