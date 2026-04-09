<?php

namespace App\Domain\Contracts;

interface CategoryRepositoryInterface
{
    public function listAllOrderedByName(): array;
    public function existsById(string $id): bool;
    public function create(string $name): array;
}
