<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$categoryRepo = new CategoryRepository();
$favorites    = new Favorites();

$categories = $categoryRepo->getAll();

$pageTitle      = 'Kategorie receptů – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/kategorie-receptu.php';
