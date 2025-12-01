<?php

declare(strict_types=1);

namespace App\Http\Services\User;

use App\Http\Repositories\Eloquent\UserRepostrie;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class UserService
{
    public function __construct(
        private UserRepostrie $user_repostrie,
    ) {}

    /**
     * Get info for the authenticated user.
     */
    public function get_user_info(): array
    {
        $user = $this->user_repostrie->get_user_info();

        return [
            'user' => $user,
        ];
    }

    /**
     * Get info for a user by ID.
     */
    public function get_user_info_by_id(int $user_id): array
    {
        $user = $this->user_repostrie->get_user_by_id($user_id);

        return [
            'user' => $user,
        ];
    }

    /**
     * Delete a user by ID.
     */
    public function delete_user(int $user_id): array
    {
        $deleted = $this->user_repostrie->delete_user_by_id($user_id);

        return [
            'deleted' => $deleted,
        ];
    }

    /**
     * Update a user by ID.
     */
    public function update_user(int $user_id, array $data): array
    {
        $user = $this->user_repostrie->update_user_by_id($user_id, $data);

        return [
            'user' => $user,
        ];
    }
    public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->user_repostrie->paginate($perPage, $filters);
    }
}
