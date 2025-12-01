<?php

namespace App\Http\Controllers\Api\V1\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Ad\CreateAdRequest;
use App\Http\Requests\Api\V1\Ad\UpdateAdRequest;
use App\Http\Resources\AdResource;
use App\Http\Services\Ads\AdsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdsController extends BaseApiController
{
  public function __construct(
    private readonly AdsService $adService,
  ) {}

  public function index(Request $request): JsonResponse
  {
    try {
      $perPage = (int) $request->query('per_page', 15);

      $filters = [
        'search' => $request->query('search'),
      ];

      $ads = $this->adService->getAll($perPage, $filters);

      return $this->successResponse(
        message: "Ads retrieved",
        statusCode: Response::HTTP_OK,
        data: [
          "ads" => AdResource::collection($ads),
          "meta" => [
            "total" => $ads->total(),
            "per_page" => $ads->perPage(),
            "current_page" => $ads->currentPage(),
            "last_page" => $ads->lastPage(),
          ]
        ]
      );
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to get Ads: " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function store(CreateAdRequest $request): JsonResponse
  {
    try {
      $data = $request->validated();

      if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('ads', 'public');
      }

      $created = $this->adService->create_ad($data);

      return $this->successResponse(
        message: "Ad Created",
        statusCode: Response::HTTP_CREATED,
        data: $created
      );
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to create ad: " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function show($ad_id): JsonResponse
  {
    try {
      $data = $this->adService->get_ad_by_id((int)$ad_id);

      return $this->successResponse("Ad retrieved", Response::HTTP_OK, $data);
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to get ad: " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function update(UpdateAdRequest $request, $ad_id): JsonResponse
  {
    try {
      $data = $request->validated();

      if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('ads', 'public');
      }

      $updated = $this->adService->update_ad((int)$ad_id, $data);

      return $this->successResponse("Ad updated", Response::HTTP_OK, $updated);
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to update ad: " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function destroy($ad_id): JsonResponse
  {
    try {
      $deleted = $this->adService->delete_ad((int)$ad_id);

      return $this->successResponse("Ad deleted", Response::HTTP_OK, $deleted);
    } catch (Exception $e) {
      return $this->errorResponse(
        message: "Failed to delete ad: " . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }
}
