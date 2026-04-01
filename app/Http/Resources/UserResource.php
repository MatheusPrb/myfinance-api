<?php

namespace App\Interfaces\Http\Resources;

use App\Domain\Entities\User;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id(),
            'name' => $user->name(),
            'email' => $user->email(),
            'created_at' => $user->createdAt()?->format(DateTimeInterface::ATOM),
            'updated_at' => $user->updatedAt()?->format(DateTimeInterface::ATOM),
        ];
    }
}
