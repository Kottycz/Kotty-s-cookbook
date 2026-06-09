<?php

declare(strict_types=1);

/**
 * Oblíbené recepty.
 *
 * Přihlášení uživatelé: oblíbené se ukládají do tabulky user_favorites v DB.
 * Nepřihlášení uživatelé: oblíbené zůstávají v session (jako dříve).
 *
 * Při přihlášení se zavolá loadForUser() – sloučí session a DB oblíbené.
 */
final class Favorites
{
    private const SESSION_KEY = 'favorites';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Přidá recept mezi oblíbené. */
    public function add(int $recipeId): void
    {
        $userId = $this->getUserId();
        if ($userId !== null) {
            Database::getConnection()
                ->prepare('INSERT IGNORE INTO user_favorites (user_id, recipe_id) VALUES (?, ?)')
                ->execute([$userId, $recipeId]);
        }

        $ids = $this->getIds();
        if (!in_array($recipeId, $ids, true)) {
            $ids[] = $recipeId;
        }
        $_SESSION[self::SESSION_KEY] = $ids;
    }

    /** Odebere recept z oblíbených. */
    public function remove(int $recipeId): void
    {
        $userId = $this->getUserId();
        if ($userId !== null) {
            Database::getConnection()
                ->prepare('DELETE FROM user_favorites WHERE user_id = ? AND recipe_id = ?')
                ->execute([$userId, $recipeId]);
        }

        $_SESSION[self::SESSION_KEY] = array_values(array_filter(
            $this->getIds(),
            fn(int $id): bool => $id !== $recipeId,
        ));
    }

    /** Přepne stav oblíbeného receptu. */
    public function toggle(int $recipeId): void
    {
        if ($this->contains($recipeId)) {
            $this->remove($recipeId);
        } else {
            $this->add($recipeId);
        }
    }

    /** Je recept mezi oblíbenými? */
    public function contains(int $recipeId): bool
    {
        return in_array($recipeId, $this->getIds(), true);
    }

    /** Vrátí pole ID všech oblíbených receptů. */
    public function getIds(): array
    {
        $ids = $_SESSION[self::SESSION_KEY] ?? [];
        return array_values(array_map(intval(...), array_filter($ids, is_numeric(...))));
    }

    /** Počet oblíbených receptů. */
    public function count(): int
    {
        return count($this->getIds());
    }

    /** Je seznam prázdný? */
    public function isEmpty(): bool
    {
        return $this->getIds() === [];
    }

    /** Vyprázdní session (DB oblíbené zůstávají). */
    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }

    /**
     * Volá se při přihlášení uživatele.
     * Načte oblíbené z DB, sloučí se session oblíbenými a uloží vše zpět.
     */
    public static function loadForUser(int $userId): void
    {
        $db = Database::getConnection();

        // Načti z DB
        $stmt = $db->prepare('SELECT recipe_id FROM user_favorites WHERE user_id = ?');
        $stmt->execute([$userId]);
        $dbIds = array_map('intval', array_column($stmt->fetchAll(), 'recipe_id'));

        // Session oblíbené (přidané před přihlášením)
        $sessionIds = array_values(array_map(
            intval(...),
            array_filter($_SESSION[self::SESSION_KEY] ?? [], is_numeric(...)),
        ));

        // Sloučení
        $merged = array_values(array_unique(array_merge($dbIds, $sessionIds)));

        // Ulož session oblíbené do DB (pokud tam nejsou)
        if ($sessionIds !== []) {
            $ins = $db->prepare('INSERT IGNORE INTO user_favorites (user_id, recipe_id) VALUES (?, ?)');
            foreach ($sessionIds as $id) {
                $ins->execute([$userId, $id]);
            }
        }

        $_SESSION[self::SESSION_KEY] = $merged;
    }

    // ── Soukromé pomocné metody ──────────────────────────────────────────────

    private function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}
