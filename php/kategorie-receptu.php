<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$categoryRepo = new CategoryRepository();
$favorites    = new Favorites();

$categories = $categoryRepo->getAll();

$pageTitle       = 'Kategorie receptů – Kottyho kuchařka';
$pageDescription = 'Přehled všech kategorií receptů na Kottyho kuchařce – polévky, hlavní jídla, saláty, těstoviny, moučníky a nápoje.';
$favoritesCount  = $favorites->count();

require __DIR__ . '/views/kategorie-receptu.php';
