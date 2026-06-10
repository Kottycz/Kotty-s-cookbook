<?php

/**
 * PARTIAL: Hlavička stránky
 *
 * Očekává proměnnou:
 *   $pageTitle (string) – titulek stránky
 *
 * Volitelně:
 *   $favoritesCount (int) – počet receptů v oblíbených (výchozí 0)
 */

$pageTitle       ??= 'Kottyho kuchařka';
$pageDescription ??= 'Kottyho kuchařka – objevujte, přidávejte a sdílejte oblíbené recepty.';
$favoritesCount  ??= 0;

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <link rel="stylesheet" href="../assets/css/main.css?v=<?= filemtime(__DIR__ . '/../../assets/css/main.css') ?>">
    <style>
        @media (max-width: 768px) {
            .recipe-categories-grid {
                display: flex !important;
                flex-direction: row !important;
                overflow-x: auto !important;
                scroll-snap-type: x mandatory !important;
                gap: 12px !important;
                padding: 4px 16px 20px !important;
                scrollbar-width: none !important;
            }
            .recipe-category {
                flex: 0 0 47vw !important;
                height: 55vw !important;
                border-radius: 20px !important;
                box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
            }
            .recipe-category span {
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 8px !important;
                font-size: 22px !important;
                font-weight: 700 !important;
                background: rgba(0,0,0,0.38) !important;
                letter-spacing: 0.3px !important;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <img src="../assets/images/logo2.png" alt="Logo" class="logo-img">
        <a href="index.php" class="logo-link">
            <h1>Kottyho kuchařka</h1>
        </a>
    </div>

    <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <nav class="navigation" id="navigation">
        <ul>
            <li><a href="recepty.php">Recepty</a></li>
            <li>
                <a href="oblibene.php">
                    Oblíbené<?php if ($favoritesCount > 0): ?> <span class="header__favorites-badge"><?= $favoritesCount ?></span><?php endif; ?>
                </a>
            </li>
            <?php if (Auth::isLoggedIn()): ?>
                <li><a href="pridat-recept.php">Přidat recept</a></li>
                <li><a href="moje-recepty.php">Moje recepty</a></li>
            <?php endif; ?>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o-nas.php">O nás</a></li>
            <!-- Desktop: ikonka s dropdownem -->
            <li class="nav-user-item">
                <button class="nav-avatar-btn" id="user-menu-btn" aria-label="Účet">
                    <?php if (Auth::isLoggedIn()): ?>
                        <span class="nav-avatar-initial"><?= htmlspecialchars(strtoupper(mb_substr($_SESSION['user_email'], 0, 1))) ?></span>
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7H4z"/>
                        </svg>
                    <?php endif; ?>
                </button>
                <div class="nav-user-dropdown" id="user-dropdown">
                    <?php if (Auth::isLoggedIn()): ?>
                        <div class="dropdown-email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
                        <a href="odhlasit.php" class="dropdown-link dropdown-link--logout">Odhlásit se</a>
                    <?php else: ?>
                        <a href="prihlasit.php" class="dropdown-link">Přihlásit se</a>
                        <a href="registrovat.php" class="dropdown-link">Registrovat se</a>
                    <?php endif; ?>
                </div>
            </li>
            <!-- Mobil: přímé odkazy (bez dropdownu) -->
            <?php if (Auth::isLoggedIn()): ?>
                <li class="nav-user-mobile"><a href="odhlasit.php">Odhlásit se</a></li>
            <?php else: ?>
                <li class="nav-user-mobile"><a href="prihlasit.php">Přihlásit se</a></li>
                <li class="nav-user-mobile"><a href="registrovat.php">Registrovat se</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
