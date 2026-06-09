<?php

declare(strict_types=1);

readonly class RecipeDTO {

	public function __construct(
		public int     $id,
		public int     $categoryId,
		public int     $difficultyId,
		public string  $name,
		public string  $slug,
		public string  $description,
		public string  $image,
		public int     $prepTimeMinutes,
		public int     $cookTimeMinutes,
		public int     $servings,
		public bool    $featured,
		public string  $createdAt,
		public ?string $categoryName   = null,
		public ?string $categorySlug   = null,
		public ?string $difficultyName = null,
		// Pole pro uživatelské recepty
		public ?int    $userId         = null,
		public string  $visibility     = 'public',
		public string  $status         = 'approved',
		public ?string $approvalToken  = null,
	) {}

	/**
	 * @param array<string, mixed> $row
	 */
	public static function fromRow(array $row): self {
		return new self(
			id:             (int) $row['id'],
			categoryId:     (int) $row['category_id'],
			difficultyId:   (int) $row['difficulty_id'],
			name:           $row['name'],
			slug:           $row['slug'],
			description:    $row['description'],
			image:          $row['image'],
			prepTimeMinutes:(int) $row['prep_time_minutes'],
			cookTimeMinutes:(int) $row['cook_time_minutes'],
			servings:       (int) $row['servings'],
			featured:       (bool) $row['featured'],
			createdAt:      $row['created_at'],
			categoryName:   $row['category_name'] ?? null,
			categorySlug:   $row['category_slug'] ?? null,
			difficultyName: $row['difficulty_name'] ?? null,
			userId:         isset($row['user_id']) && $row['user_id'] !== null ? (int) $row['user_id'] : null,
			visibility:     $row['visibility'] ?? 'public',
			status:         $row['status'] ?? 'approved',
			approvalToken:  $row['approval_token'] ?? null,
		);
	}

	/** Celkový čas přípravy v minutách */
	public function getTotalTimeMinutes(): int {
		return $this->prepTimeMinutes + $this->cookTimeMinutes;
	}

	/** Lidsky čitelný formát celkového času, např. "1 h 30 min" */
	public function getFormattedTotalTime(): string {
		$total   = $this->getTotalTimeMinutes();
		$hours   = intdiv($total, 60);
		$minutes = $total % 60;

		if ($hours === 0) return $minutes . ' min';
		return $minutes === 0 ? $hours . ' h' : $hours . ' h ' . $minutes . ' min';
	}

	/** Je recept soukromý? */
	public function isPrivate(): bool {
		return $this->visibility === 'private';
	}

	/** Čeká recept na schválení? */
	public function isPending(): bool {
		return $this->status === 'pending';
	}
}
