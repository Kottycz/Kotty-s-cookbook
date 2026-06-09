<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: moje-recepty.php');
    exit;
}

csrf_verify();

$recipeRepo = new RecipeRepository();
$user       = Auth::currentUser();
$recipeId   = (int) ($_POST['recipe_id'] ?? 0);
$recipe     = $recipeRepo->getById($recipeId);

if ($recipe === null) {
    header('Location: moje-recepty.php');
    exit;
}

// Kontrola: majitel nebo admin
if ($recipe->userId !== $user->id && !Auth::isAdmin()) {
    header('Location: moje-recepty.php');
    exit;
}

// Recept již čeká nebo je veřejný
if ($recipe->isPending() || !$recipe->isPrivate()) {
    $_SESSION['recipe_flash'] = 'Recept již byl odeslán nebo je zveřejněný.';
    header('Location: recept.php?slug=' . urlencode($recipe->slug));
    exit;
}

// Vygeneruj token a nastav pending
$token = bin2hex(random_bytes(32));
$recipeRepo->requestApproval($recipe->id, $token);

// Schvalovací odkaz
$protocol    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$dir         = rtrim(dirname($_SERVER['PHP_SELF']), '/');
$approvalUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $dir . '/schvalit-recept.php?token=' . urlencode($token);

Mailer::sendApproval($recipe->name, $user->email, $approvalUrl);

$_SESSION['recipe_flash'] = 'Recept byl odeslán ke schválení. Přijde ti email s odkazem pro zveřejnění.';
header('Location: recept.php?slug=' . urlencode($recipe->slug));
exit;
