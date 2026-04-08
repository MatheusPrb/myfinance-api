<?php

namespace App\Application\UseCases\CreateExpense;

final readonly class CreateExpenseInput
{
    public function __construct(
        public readonly string $userId,
        public readonly string $categoryId,
        public readonly ?string $subcategoryId,
        public readonly ?string $description,
        public readonly string $value,
    ) {}

    /**
     * @param  array{user_id: string, category_id: string, subcategory_id?: string|null, description?: string|null, value: string|float|int}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['category_id'],
            $data['subcategory_id'] ?? null,
            $data['description'] ?? null,
            (string) $data['value'],
        );
    }
}
