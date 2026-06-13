<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo     = new RecipeRepository();
$difficultyRepo = new DifficultyRepository();
$favorites      = new Favorites();

$difficulties = $difficultyRepo->getAll();

$difficultyId = isset($_GET['difficulty']) && $_GET['difficulty'] !== '' ? (int) $_GET['difficulty'] : null;
$time         = in_array($_GET['time'] ?? '', ['do30', 'do60', 'nad60'], true) ? $_GET['time'] : null;

$recipes = ($difficultyId !== null || $time !== null)
    ? $recipeRepo->getFiltered($difficultyId, $time)
    : $recipeRepo->getAll();

$pageTitle       = 'Recepty – Kottyho kuchařka';
$pageDescription = 'Procházejte všechny recepty na Kottyho kuchařce – od polévek přes hlavní jídla až po dezerty a moučníky.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/recepty.php';
