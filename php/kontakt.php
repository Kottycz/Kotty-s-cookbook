<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$favorites = new Favorites();

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $jmeno  = trim($_POST['jmeno'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $zprava = trim($_POST['zprava'] ?? '');

    if ($jmeno === '') {
        $errors[] = 'Zadejte své jméno.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Zadejte platný e-mail.';
    }
    if ($zprava === '') {
        $errors[] = 'Zpráva nesmí být prázdná.';
    }

    if ($errors === []) {
        $subject = 'Zpráva z kontaktního formuláře od ' . $jmeno;
        $body    = "Jméno: {$jmeno}\nE-mail: {$email}\n\nZpráva:\n{$zprava}";

        Mailer::send('kottyho.kucharka@gmail.com', $subject, $body, $email);
        $success = true;
    }
}

$pageTitle       = 'Kontakt – Kottyho kuchařka';
$pageDescription = 'Napište nám – kontaktní formulář Kottyho kuchařky. Rádi odpovíme na vaše dotazy a připomínky.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/kontakt.php';
