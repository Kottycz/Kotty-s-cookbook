<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$favorites = new Favorites();

if (Auth::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors  = [];
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errors[] = 'Zadej e-mail.';
    } elseif ($password === '') {
        $errors[] = 'Zadej heslo.';
    } elseif (!Auth::login($email, $password)) {
        $errors[] = 'Nesprávný e-mail nebo heslo.';
    } else {
        header('Location: index.php');
        exit;
    }
}

$pageTitle       = 'Přihlásit se – Kottyho kuchařka';
$pageDescription = 'Přihlaste se do Kottyho kuchařky a přidávejte vlastní recepty.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/prihlasit.php';
