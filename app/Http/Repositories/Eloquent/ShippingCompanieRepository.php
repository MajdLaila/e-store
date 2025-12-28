<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use App\Models\Ad;
use App\Models\ShippingCompanyLocation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder;

final readonly class ShippingCompanieRepository
{
  public function __construct(
    private ShippingCompanyLocation $shippingCompanie
  ) {}

  public function create_shipping_location(array $data)
  {
    return $this->shippingCompanie->create($data);
  }

  public function buildQuery(array $filters = []): Builder
  {
    $query = $this->shippingCompanie->newQuery()->select([
      'address',
      'phone',
      'lang',
      'lat',
      'name'
    ]);


    if (!empty($filters['search'])) {
      $query->where('name', 'like', "%" . $filters['search'] . "%");
    }

    return $query;
  }

  public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
  {
    $query = $this->buildQuery($filters);
    return $query->paginate($perPage);
  }

  public function get_shipping_company_by_id(int $ad_id)
  {
    $shipping_company = $this->shippingCompanie->find($ad_id);
    if (!$shipping_company) {
      throw new ModelNotFoundException("shipping_company not found");
    }
    return $shipping_company;
  }

  public function update_shipping_company(int $shipping_company_id, array $data)
  {
    $shipping_company = $this->get_shipping_company_by_id($shipping_company_id);
    $shipping_company->fill($data);
    $shipping_company->save();
    return $shipping_company;
  }

  public function delete_shipping_company(int $shipping_company_id): bool
  {
    $shipping_company = $this->get_shipping_company_by_id($shipping_company_id);
    return $shipping_company->delete();
  }
}
