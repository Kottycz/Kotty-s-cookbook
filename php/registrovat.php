<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$favorites = new Favorites();

// ── reCAPTCHA klíče ──────────────────────────────────────────────────────────
// Zaregistruj se na: https://www.google.com/recaptcha/admin/create
// Typ: reCAPTCHA v2 → "Nejsem robot"
define('RECAPTCHA_SITE_KEY',   '6Lchnw4tAAAAAHnlkTCm6qCboUUEWKS9Tbl3AT18');  // <- Sem vlož Site Key
define('RECAPTCHA_SECRET_KEY', '6Lchnw4tAAAAAIhaucCzlnpVTivMVnLJWPnbvDyT');  // <- Sem vlož Secret Key
// ────────────────────────────────────────────────────────────────────────────

if (Auth::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Ověření reCAPTCHA (pouze pokud jsou klíče nastaveny)
    if (RECAPTCHA_SECRET_KEY !== '') {
        $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $verifyData      = http_build_query([
            'secret'   => RECAPTCHA_SECRET_KEY,
            'response' => $captchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
        $ctx    = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $verifyData,
        ]]);
        $result = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $ctx);
        $json   = $result ? json_decode($result, true) : [];

        if (!($json['success'] ?? false)) {
            $errors[] = 'Ověření "Nejsem robot" selhalo. Zkuste to znovu.';
        }
    }

    if ($errors === []) {
        if ($email === '') {
            $errors[] = 'Zadej e-mail.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Neplatný formát e-mailu.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Heslo musí mít alespoň 6 znaků.';
        } elseif ($password !== $password2) {
            $errors[] = 'Hesla se neshodují.';
        } else {
            $repo = new UserRepository();
            if ($repo->emailExists($email)) {
                $errors[] = 'Účet s tímto e-mailem již existuje.';
            } else {
                Auth::register($email, $password);
                Auth::login($email, $password);
                header('Location: index.php');
                exit;
            }
        }
    }
}

$pageTitle       = 'Registrace – Kottyho kuchařka';
$pageDescription = 'Zaregistrujte se na Kottyho kuchařce a začněte přidávat vlastní recepty zdarma.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/registrovat.php';
