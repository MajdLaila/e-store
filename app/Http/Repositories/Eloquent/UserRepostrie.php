<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 *
 * Handles the creation, retrieval, updating, and deletion of users.
 */
final readonly class UserRepostrie
{
    public function __construct(
        private User $user,
    ) {}

    public function findByEmail(string $email): User
    {
        $user = $this->user->where('email', $email)->first();

        if (! $user) {
            throw new ModelNotFoundException('User not found');
        }

        return $user;
    }

    public function get_user_info(): User
    {
        $user = Auth::user();

        if (! $user) {
            throw new \Exception('User not authenticated');
        }

        return $user;
    }

    public function get_user_by_id($user_id): User
    {
        $user = $this->user->where('id', $user_id)->first();

        if (! $user) {
            throw new ModelNotFoundException('User not found');
        }

        return $user;
    }

    public function delete_user_by_id(int $user_id): bool
    {
        $user = $this->user->find($user_id);

        if (! $user) {
            throw new ModelNotFoundException('User not found');
        }

        return $user->delete();
    }

    public function update_user_by_id(int $user_id, array $data): User
    {
        $user = $this->user->find($user_id);

        if (! $user) {
            throw new ModelNotFoundException('User not found');
        }

        // Optional: handle password hashing if password is provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // don't update password if not provided
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

   
    public function buildQuery(array $filters = [], array $columns = []): Builder
    {
        $columns = $columns ?: [
            'id', 'name', 'email', 'phone', 'avatar', 'is_active', 'is_admin', 'created_at'
        ];

        $query = $this->user->newQuery()->select($columns);

        // SEARCH
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

 
       

        return $query;
    }

    public function paginate(int $perPage = 15, array $filters = [], array $columns = []): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 100)); // حماية من perPage كبير جداً
        $query = $this->buildQuery($filters, $columns);

        // لو حاب تعمل eager load على علاقات تمر عبر $filters مثلاً: if(isset($filters['with'])) { $query->with($filters['with']); }
        return $query->paginate($perPage);
    }
}
