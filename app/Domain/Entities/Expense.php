<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

final class Expense
{
    public function __construct(
        private string $id,
        private string $userId,
        private string $categoryId,
        private ?string $subcategoryId,
        private ?string $description,
        private string $value,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function subcategoryId(): ?string
    {
        return $this->subcategoryId;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
