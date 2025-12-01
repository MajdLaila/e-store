<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**

 *
 * Handles the creation and retrieval of bus drivers.
 */
final readonly class AuthRepostrie
{
  public function __construct(
    private User $user,
  ) {}
  public function findByEmail(string $email): User
  {
    $user = $this->user->where('email', $email)->first();



    return $user;
  }
  public function createuser($data)
  {
    $user = $this->user->create($data);
    return $user;
  }
}
