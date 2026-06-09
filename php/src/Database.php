<?php

declare(strict_types=1);

require_once __DIR__ . '/db_config.php';

final class Database {

	private static ?PDO $connection = NULL;

	private function __construct() {
	}

	public static function getConnection(): PDO {
		if (self::$connection === NULL) {
			self::$connection = new PDO(
				dsn:      'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
				username: DB_USER,
				password: DB_PASS,
				options:  [
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES   => FALSE,
				],
			);
		}

		return self::$connection;
	}

}
