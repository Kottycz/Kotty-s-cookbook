<?php

declare(strict_types=1);

final class RecipeRepository
{

	private PDO $db;

	/** Společný SELECT – JOINuje kategorie a obtížnosti. */
	private const BASE_SELECT = '
		SELECT r.*,
			c.name AS category_name,
			c.slug AS category_slug,
			d.name AS difficulty_name
		FROM recipes r
		JOIN categories c ON r.category_id = c.id
		JOIN difficulties d ON r.difficulty_id = d.id
	';

	/** Filtr pro veřejné stránky – pouze zveřejněné a schválené recepty. */
	private const PUBLIC_FILTER = " r.visibility = 'public' AND r.status = 'approved'";

	public function __construct()
	{
		$this->db = Database::getConnection();
	}

	/** Vrátí všechny veřejné schválené recepty. */
	public function getAll(): array
	{
		$stmt = $this->db->query(self::BASE_SELECT . ' WHERE' . self::PUBLIC_FILTER . ' ORDER BY r.created_at DESC');
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Najde recept podle ID (bez omezení viditelnosti). */
	public function getById(int $id): ?RecipeDTO
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . ' WHERE r.id = :id');
		$stmt->execute(['id' => $id]);
		$row = $stmt->fetch();
		return $row ? RecipeDTO::fromRow($row) : null;
	}

	/** Najde recept podle slugu (bez omezení – pro detail + edit). */
	public function getBySlug(string $slug): ?RecipeDTO
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . ' WHERE r.slug = :slug');
		$stmt->execute(['slug' => $slug]);
		$row = $stmt->fetch();
		return $row ? RecipeDTO::fromRow($row) : null;
	}

	/** Vrátí veřejné recepty v dané kategorii. */
	public function getByCategory(int $categoryId): array
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . '
			WHERE' . self::PUBLIC_FILTER . ' AND r.category_id = :categoryId
			ORDER BY r.created_at DESC
		');
		$stmt->execute(['categoryId' => $categoryId]);
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí veřejné recepty podle slugu kategorie. */
	public function getByCategorySlug(string $slug): array
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . '
			WHERE' . self::PUBLIC_FILTER . " AND c.slug = :slug
			ORDER BY r.created_at DESC
		");
		$stmt->execute(['slug' => $slug]);
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí doporučené (featured) veřejné recepty. */
	public function getFeatured(int $limit = 6): array
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . '
			WHERE' . self::PUBLIC_FILTER . ' AND r.featured = 1
			ORDER BY r.created_at DESC
			LIMIT :limit
		');
		$stmt->bindValue('limit', $limit, PDO::PARAM_INT);
		$stmt->execute();
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí náhodně vybrané veřejné recepty – výběr se mění každou hodinu. */
	public function getFeaturedHourly(int $limit = 6): array
	{
		$all = $this->getAll();
		mt_srand((int) date('YmdH'));
		shuffle($all);
		return array_slice($all, 0, $limit);
	}

	/** Vrátí oblíbené recepty podle pole ID. */
	public function getByIds(array $ids): array
	{
		if ($ids === []) return [];

		$placeholders = implode(',', array_fill(0, count($ids), '?'));
		$stmt = $this->db->prepare(self::BASE_SELECT . "
			WHERE r.id IN ($placeholders)
			ORDER BY r.name
		");
		$stmt->execute(array_values($ids));
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vyhledá veřejné recepty podle názvu, popisu nebo ingredience. */
	public function search(string $query): array
	{
		$escaped = str_replace(['%', '_'], ['\\%', '\\_'], $query);
		$like    = '%' . $escaped . '%';

		$stmt = $this->db->prepare(self::BASE_SELECT . "
			WHERE" . self::PUBLIC_FILTER . "
				AND (
					r.name LIKE :q1
					OR r.description LIKE :q2
					OR EXISTS (
						SELECT 1 FROM recipe_ingredients i
						WHERE i.recipe_id = r.id AND i.name LIKE :q3
					)
				)
			ORDER BY r.name
		");
		$stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí veřejné recepty filtrované podle obtížnosti a celkového času. */
	public function getFiltered(?int $difficultyId, ?string $time): array
	{
		$where = 'WHERE' . self::PUBLIC_FILTER;
		$params = [];

		if ($difficultyId !== null) {
			$where .= ' AND r.difficulty_id = :difficultyId';
			$params['difficultyId'] = $difficultyId;
		}

		if ($time === 'do30') {
			$where .= ' AND (r.prep_time_minutes + r.cook_time_minutes) <= 30';
		} elseif ($time === 'do60') {
			$where .= ' AND (r.prep_time_minutes + r.cook_time_minutes) <= 60';
		} elseif ($time === 'nad60') {
			$where .= ' AND (r.prep_time_minutes + r.cook_time_minutes) > 60';
		}

		$stmt = $this->db->prepare(self::BASE_SELECT . ' ' . $where . ' ORDER BY r.created_at DESC');
		$stmt->execute($params);
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí všechny recepty daného uživatele (veřejné i soukromé). */
	public function getByUserId(int $userId): array
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . '
			WHERE r.user_id = :userId
			ORDER BY r.created_at DESC
		');
		$stmt->execute(['userId' => $userId]);
		return array_map(RecipeDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Najde recept podle schvalovacího tokenu. */
	public function getByApprovalToken(string $token): ?RecipeDTO
	{
		$stmt = $this->db->prepare(self::BASE_SELECT . ' WHERE r.approval_token = :token');
		$stmt->execute(['token' => $token]);
		$row = $stmt->fetch();
		return $row ? RecipeDTO::fromRow($row) : null;
	}

	/** Vrátí obrázky galerie pro daný recept. */
	public function getImages(int $recipeId): array
	{
		$stmt = $this->db->prepare('
			SELECT * FROM recipe_images
			WHERE recipe_id = :recipeId
			ORDER BY sort_order
		');
		$stmt->execute(['recipeId' => $recipeId]);
		return array_map(RecipeImageDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí ingredience daného receptu. */
	public function getIngredients(int $recipeId): array
	{
		$stmt = $this->db->prepare('
			SELECT i.*, u.name AS unit_name, u.abbreviation AS unit_abbreviation
			FROM recipe_ingredients i
			LEFT JOIN units u ON i.unit_id = u.id
			WHERE i.recipe_id = :recipeId
			ORDER BY i.sort_order, i.id
		');
		$stmt->execute(['recipeId' => $recipeId]);
		return array_map(IngredientDTO::fromRow(...), $stmt->fetchAll());
	}

	/** Vrátí kroky postupu daného receptu. */
	public function getSteps(int $recipeId): array
	{
		$stmt = $this->db->prepare('
			SELECT * FROM recipe_steps
			WHERE recipe_id = :recipeId
			ORDER BY step_number
		');
		$stmt->execute(['recipeId' => $recipeId]);
		return array_map(RecipeStepDTO::fromRow(...), $stmt->fetchAll());
	}

	/**
	 * Vytvoří nový recept a vrátí jeho slug.
	 * Výchozí hodnoty zachovávají zpětnou kompatibilitu.
	 */
	public function create(
		int     $categoryId,
		int     $difficultyId,
		string  $name,
		string  $description,
		string  $image,
		int     $prepTimeMinutes,
		int     $cookTimeMinutes,
		int     $servings,
		?int    $userId     = null,
		string  $visibility = 'public',
		string  $status     = 'approved',
	): string {
		$slug = $this->generateUniqueSlug($name);

		$stmt = $this->db->prepare('
			INSERT INTO recipes
				(user_id, category_id, difficulty_id, name, slug, description, image,
				 prep_time_minutes, cook_time_minutes, servings, featured, visibility, status)
			VALUES
				(:userId, :categoryId, :difficultyId, :name, :slug, :description, :image,
				 :prepTime, :cookTime, :servings, 0, :visibility, :status)
		');
		$stmt->execute([
			'userId'      => $userId,
			'categoryId'  => $categoryId,
			'difficultyId'=> $difficultyId,
			'name'        => $name,
			'slug'        => $slug,
			'description' => $description,
			'image'       => $image,
			'prepTime'    => $prepTimeMinutes,
			'cookTime'    => $cookTimeMinutes,
			'servings'    => $servings,
			'visibility'  => $visibility,
			'status'      => $status,
		]);

		return $slug;
	}

	/** Aktualizuje základní údaje receptu. */
	public function update(
		int    $id,
		int    $categoryId,
		int    $difficultyId,
		string $name,
		string $description,
		string $image,
		int    $prepTimeMinutes,
		int    $cookTimeMinutes,
		int    $servings,
	): void {
		$stmt = $this->db->prepare('
			UPDATE recipes SET
				category_id = :categoryId,
				difficulty_id = :difficultyId,
				name = :name,
				description = :description,
				image = :image,
				prep_time_minutes = :prepTime,
				cook_time_minutes = :cookTime,
				servings = :servings
			WHERE id = :id
		');
		$stmt->execute([
			'id'          => $id,
			'categoryId'  => $categoryId,
			'difficultyId'=> $difficultyId,
			'name'        => $name,
			'description' => $description,
			'image'       => $image,
			'prepTime'    => $prepTimeMinutes,
			'cookTime'    => $cookTimeMinutes,
			'servings'    => $servings,
		]);
	}

	/** Nastaví recept jako čekající na schválení a uloží token. */
	public function requestApproval(int $id, string $token): void
	{
		$this->db->prepare("
			UPDATE recipes SET status = 'pending', approval_token = :token WHERE id = :id
		")->execute(['token' => $token, 'id' => $id]);
	}

	/** Zamítne recept – vrátí ho do soukromého stavu a smaže token. */
	public function reject(int $id): void
	{
		$this->db->prepare("
			UPDATE recipes SET status = 'approved', visibility = 'private', approval_token = NULL WHERE id = :id
		")->execute(['id' => $id]);
	}

	/** Schválí recept – zveřejní ho a smaže token. */
	public function approve(int $id): void
	{
		$this->db->prepare("
			UPDATE recipes SET visibility = 'public', status = 'approved', approval_token = NULL WHERE id = :id
		")->execute(['id' => $id]);
	}

	/** Smaže všechny ingredience receptu a vloží nové. */
	public function replaceIngredients(int $recipeId, array $lines): void
	{
		$this->db->prepare('DELETE FROM recipe_ingredients WHERE recipe_id = :id')
			->execute(['id' => $recipeId]);

		$stmt = $this->db->prepare('
			INSERT INTO recipe_ingredients (recipe_id, name, sort_order)
			VALUES (:recipeId, :name, :sortOrder)
		');
		foreach ($lines as $i => $line) {
			$stmt->execute(['recipeId' => $recipeId, 'name' => $line, 'sortOrder' => $i + 1]);
		}
	}

	/** Smaže všechny kroky receptu a vloží nové. */
	public function replaceSteps(int $recipeId, array $lines): void
	{
		$this->db->prepare('DELETE FROM recipe_steps WHERE recipe_id = :id')
			->execute(['id' => $recipeId]);

		$stmt = $this->db->prepare('
			INSERT INTO recipe_steps (recipe_id, step_number, description)
			VALUES (:recipeId, :stepNumber, :description)
		');
		foreach ($lines as $i => $line) {
			$stmt->execute(['recipeId' => $recipeId, 'stepNumber' => $i + 1, 'description' => $line]);
		}
	}

	/** Smaže recept (CASCADE smaže ingredience, kroky a obrázky). */
	public function delete(int $id): void
	{
		$this->db->prepare('DELETE FROM recipes WHERE id = :id')->execute(['id' => $id]);
	}

	private function generateUniqueSlug(string $name): string
	{
		$base = $this->slugify($name);
		$slug = $base;
		$i    = 2;
		while ($this->slugExists($slug)) {
			$slug = $base . '-' . $i;
			$i++;
		}
		return $slug;
	}

	private function slugify(string $text): string
	{
		$map  = ['á'=>'a','č'=>'c','ď'=>'d','é'=>'e','ě'=>'e','í'=>'i','ň'=>'n','ó'=>'o','ř'=>'r','š'=>'s','ť'=>'t','ú'=>'u','ů'=>'u','ý'=>'y','ž'=>'z'];
		$text = strtr(mb_strtolower($text, 'UTF-8'), $map);
		$text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);
		return trim($text, '-');
	}

	private function slugExists(string $slug): bool
	{
		$stmt = $this->db->prepare('SELECT 1 FROM recipes WHERE slug = :slug');
		$stmt->execute(['slug' => $slug]);
		return $stmt->fetchColumn() !== false;
	}
}
