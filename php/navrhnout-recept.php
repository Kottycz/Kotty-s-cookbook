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

$recipe = $recipeRepo->getById($recipeId);

// Kontrola: recept existuje, patří uživateli a je soukromý
if ($recipe === null || $recipe->userId !== $user->id || !$recipe->isPrivate()) {
    header('Location: moje-recepty.php');
    exit;
}

// Recept již čeká na schválení
if ($recipe->isPending()) {
    $_SESSION['recipe_flash'] = 'Recept již čeká na schválení administrátorem.';
    header('Location: recept.php?slug=' . urlencode($recipe->slug));
    exit;
}

// Vygeneruj bezpečný token a ulož ho do DB
$token = bin2hex(random_bytes(32));
$recipeRepo->requestApproval($recipe->id, $token);

// Sestavení schvalovacího odkazu
$protocol    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$dir         = rtrim(dirname($_SERVER['PHP_SELF']), '/');
$approvalUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $dir . '/schvalit-recept.php?token=' . urlencode($token);

Mailer::sendApproval($recipe->name, $user->email, $approvalUrl);

$_SESSION['recipe_flash'] = 'Návrh byl odeslán. Recept bude zveřejněn po schválení administrátorem.';
header('Location: recept.php?slug=' . urlencode($recipe->slug));
exit;
