<?php

declare(strict_types=1);

final class Auth
{
    private const ADMIN_EMAILS = [
        'kotrbamaty@gmail.com',
        'kottyho.kucharka@gmail.com',
        'bara.gajdova@gmail.com',
    ];

    public static function login(string $email, string $password): bool
    {
        $repo = new UserRepository();
        $hash = $repo->getPasswordHash($email);
        if ($hash === null || !password_verify($password, $hash)) {
            return false;
        }
        $user = $repo->findByEmail($email);
        if ($user === null) return false;

        session_regenerate_id(true);
        $_SESSION['user_id']    = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role']  = $user->role;

        // Načti oblíbené z DB a slouč se session oblíbenými
        Favorites::loadForUser($user->id);

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function register(string $email, string $password): int
    {
        $role = in_array(strtolower(trim($email)), self::ADMIN_EMAILS, true) ? 'admin' : 'user';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $repo = new UserRepository();
        return $repo->create($email, $hash, $role);
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['user_role'] ?? '') === 'admin';
    }

    public static function currentUser(): ?UserDTO
    {
        if (!self::isLoggedIn()) return null;
        return new UserDTO(
            (int) $_SESSION['user_id'],
            (string) $_SESSION['user_email'],
            (string) $_SESSION['user_role'],
        );
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: prihlasit.php');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: prihlasit.php');
            exit;
        }
        if (!self::isAdmin()) {
            header('Location: index.php');
            exit;
        }
    }
}
