<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use App\Models\Ad;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder;

final readonly class AdRepostrie
{
    public function __construct(
        private Ad $ad
    ) {}

    public function create_ad(array $data): Ad
    {
        return $this->ad->create($data);
    }

    public function buildQuery(array $filters = []): Builder
    {
        $query = $this->ad->newQuery()->select([
            'id',
            'name',
            'image',
            'desc',
            'product_id',
            'created_at',
        ]);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%" . $filters['search'] . "%");
        }

        return $query;
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->buildQuery($filters);
        return $query->with('product')->paginate($perPage);
    }

    public function get_ad_by_id(int $ad_id): Ad
    {
        $ad = $this->ad->find($ad_id);
        if (!$ad) {
            throw new ModelNotFoundException("Ad not found");
        }
        return $ad;
    }

    public function update_ad_by_id(int $ad_id, array $data): Ad
    {
        $ad = $this->get_ad_by_id($ad_id);
        $ad->fill($data);
        $ad->save();
        return $ad;
    }

    public function delete_ad_by_id(int $ad_id): bool
    {
        $ad = $this->get_ad_by_id($ad_id);
        return $ad->delete();
    }
}
