<?php

declare(strict_types=1);

final class UserDTO
{
    public function __construct(
        public readonly int    $id,
        public readonly string $email,
        public readonly string $role,
    ) {}

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
