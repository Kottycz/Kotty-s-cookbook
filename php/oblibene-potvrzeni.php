<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$favorites = new Favorites();

$pageTitle       = 'Přidáno do oblíbených – Kottyho kuchařka';
$pageDescription = 'Recept byl přidán do vašich oblíbených na Kottyho kuchařce.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/oblibene-potvrzeni.php';
