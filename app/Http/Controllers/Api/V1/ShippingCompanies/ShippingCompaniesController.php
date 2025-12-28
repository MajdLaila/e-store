<?php

namespace App\Http\Controllers\Api\V1\ShippingCompanies;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\ShippingLocation\CreateShippingLocation;
use App\Http\Requests\Api\V1\ShippingLocation\UpdateShippingLocation;
use App\Http\Services\ShippingCompanies\ShippingCompaniesService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ShippingCompaniesController extends BaseApiController
{
  public function __construct(
    private readonly ShippingCompaniesService $shippingCompaniesService,

  ) {}

  public function index(Request $request): JsonResponse
  {
    try {
      $perPage = (int) $request->query('per_page', 15);

      $filters = [
        'search' => $request->query('search'),
      ];

      $shipping_locations = $this->shippingCompaniesService->getAll($perPage, $filters);

      return $this->successResponse(
        message: "Shipping Location  retrieved",
        statusCode: Response::HTTP_OK,
        data:$shipping_locations
      );
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to get Ads: " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function store(CreateShippingLocation $request): JsonResponse
  {
    try {
      $data = $request->validated();



      $created = $this->shippingCompaniesService->create_shipping_location($data);

      return $this->successResponse(
        message: " Created",
        statusCode: Response::HTTP_CREATED,
        data: $created
      );
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to create  " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }



  public function update(UpdateShippingLocation $request, $shipping_company_id): JsonResponse
  {
    try {
      $data = $request->validated();



      $updated = $this->shippingCompaniesService->update_shipping_company((int)$shipping_company_id, $data);

      return $this->successResponse(" updated", Response::HTTP_OK, $updated);
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to update  : " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function destroy($shipping_company_id): JsonResponse
  {
    try {
      $deleted = $this->shippingCompaniesService->delete_shipping_company((int)$shipping_company_id);

      return $this->successResponse("  deleted", Response::HTTP_OK, $deleted);
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to delete  : " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }
}
