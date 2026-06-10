<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Auth::requireLogin();

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();
$user       = Auth::currentUser();

$myRecipes = $recipeRepo->getByUserId($user->id);

// Flash zpráva po přesměrování
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pageTitle       = 'Moje recepty – Kottyho kuchařka';
$pageDescription = 'Spravujte recepty, které jste přidali na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/moje-recepty.php';
