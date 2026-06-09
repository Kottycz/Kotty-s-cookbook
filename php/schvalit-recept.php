<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$recipeRepo = new RecipeRepository();
$userRepo   = new UserRepository();
$favorites  = new Favorites();

$token    = trim($_GET['token'] ?? '');
$recipe   = ($token !== '') ? $recipeRepo->getByApprovalToken($token) : null;
$success  = false;
$rejected = false;
$error    = '';

if ($recipe === null) {
    $error = 'Odkaz je neplatný nebo byl již použit.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if (isset($_POST['approve'])) {
        // Schválení
        $recipeRepo->approve($recipe->id);
        $recipe  = $recipeRepo->getBySlug($recipe->slug);
        $success = true;
    } elseif (isset($_POST['reject'])) {
        // Zamítnutí – recept se vrátí do soukromého stavu
        $reason = trim($_POST['reason'] ?? '');
        $recipeRepo->reject($recipe->id);

        // Pošli e-mail autorovi (pokud má uživatelský účet)
        if ($recipe->userId !== null) {
            $author = $userRepo->findById($recipe->userId);
            if ($author !== null) {
                Mailer::sendRejection($recipe->name, $author->email, $reason);
            }
        }
        $rejected = true;
    }
} else {
    // GET – zobraz náhled receptu před schválením
    $ingredients = $recipeRepo->getIngredients($recipe->id);
    $steps       = $recipeRepo->getSteps($recipe->id);
}

$pageTitle      = 'Schválení receptu – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/schvalit-recept.php';
