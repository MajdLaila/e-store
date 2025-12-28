<?php

declare(strict_types=1);

namespace App\Http\Services\ShippingCompanies;

use App\Http\Repositories\Eloquent\ShippingCompanieRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ShippingCompaniesService
{
  public function __construct(
    private ShippingCompanieRepository $shippingCompanieRepository
  ) {}

  public function create_shipping_location(array $data): array
  {
    $shipping_company = $this->shippingCompanieRepository->create_shipping_location($data);
    return $shipping_company->toArray();
  }

  public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
  {
    return $this->shippingCompanieRepository->paginate($perPage, $filters);
  }



  public function update_shipping_company(int $id, array $data): array
  {
    return [
      'shipping_company' => $this->shippingCompanieRepository->update_shipping_company($id, $data),
    ];
  }

  public function delete_shipping_company(int $id): array
  {
    return [
      'deleted' => $this->shippingCompanieRepository->delete_shipping_company($id),
    ];
  }
}
