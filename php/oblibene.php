<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo = new RecipeRepository();
$favorites  = new Favorites();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorite'])) {
    csrf_verify();
    $recipeId = (int) $_POST['recipe_id'];
    if ($recipeRepo->getById($recipeId) !== null) {
        $favorites->toggle($recipeId);
    }
    session_write_close();
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$recipes = $recipeRepo->getByIds($favorites->getIds());

// Vyčisti ze session ID receptů, které už v DB neexistují
$validIds = array_map(fn($r) => $r->id, $recipes);
foreach ($favorites->getIds() as $id) {
    if (!in_array($id, $validIds, true)) {
        $favorites->remove($id);
    }
}

$pageTitle       = 'Oblíbené recepty – Kottyho kuchařka';
$pageDescription = 'Vaše oblíbené recepty uložené na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/oblibene.php';
