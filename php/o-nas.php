<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

$favorites = new Favorites();

$pageTitle      = 'O nás – Kottyho kuchařka';
$favoritesCount = $favorites->count();

require __DIR__ . '/views/o-nas.php';
