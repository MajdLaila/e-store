<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\User\UpdateUserRequste;
use App\Http\Resources\UserResource;
use App\Http\Services\User\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseApiController
{
    public function __construct(
        private readonly UserService $user_service,
    ) {}

    /**
     * Get user info
     */
    public function get_user_info(): JsonResponse
    {
        try {
            // استدعيه
            $data = $this->user_service->get_user_info();

            return $this->successResponse(
                message: 'User info retrieved successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to get user info: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function get_user_info_byid($user_id): JsonResponse
    {
        try {
            // استدعيه
            $data = $this->user_service->get_user_info_by_id((int)$user_id);

            return $this->successResponse(
                message: 'User info retrieved successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to get user info: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function delete_user($user_id): JsonResponse
    {
        try {
            // استدعيه
            $data = $this->user_service->delete_user((int)$user_id);

            return $this->successResponse(
                message: 'User Deleted successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to delete user: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update_user(UpdateUserRequste $request, $user_id): JsonResponse
    {
        try {
            // استدعيه
            $data = $this->user_service->update_user((int)$user_id, $request->all());

            return $this->successResponse(
                message: 'User updated successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to update user: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function index(Request $request): JsonResponse
    {
     try {   
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100)); // حد أقصى: 100

        $filters = [
            'search'    => $request->query('search'),
        ];

        $paginator = $this->user_service->getAll($perPage, $filters);

        // نستخدم UserResource collection — هذا يحافظ على الـ transformation
        $usersCollection = UserResource::collection($paginator);

        // لو عندك دالة successResponse، ابقي استخدمها. هنا مثال عام:
        return $this->successResponse(
            message: 'Users retrieved successfully',
            statusCode: Response::HTTP_OK,
            data: [
                'users' => $usersCollection,
                'meta'  => [
                    'total'        => $paginator->total(),
                    'per_page'     => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ]
        );
    }
    catch (Exception $exception) {
        return $this->errorResponse(
            message: 'Failed to update user: ' . $exception->getMessage(),
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
    }
}