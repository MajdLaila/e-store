<?php

declare(strict_types=1);

namespace App\Http\Services\Ads;

use App\Http\Repositories\Eloquent\AdRepository;
use App\Http\Repositories\Eloquent\AdRepostrie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class AdsService
{
    public function __construct(
        private AdRepostrie $adRepository
    ) {}

    public function create_ad(array $data): array
    {
        $ad = $this->adRepository->create_ad($data);
        return $ad->toArray();
    }

    public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->adRepository->paginate($perPage, $filters);
    }

    public function get_ad_by_id(int $id): array
    {
        return [
            'Ad' => $this->adRepository->get_ad_by_id($id),
        ];
    }

    public function update_ad(int $id, array $data): array
    {
        return [
            'Ad' => $this->adRepository->update_ad_by_id($id, $data),
        ];
    }

    public function delete_ad(int $id): array
    {
        return [
            'deleted' => $this->adRepository->delete_ad_by_id($id),
        ];
    }
}
