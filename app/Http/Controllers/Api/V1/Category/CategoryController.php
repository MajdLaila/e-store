<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Category\CategoryUpdateRequste;
use App\Http\Requests\Api\V1\Category\CreateCategoryRequst;
use App\Http\Resources\CategoryResource;
use App\Http\Services\Category\CategoryService;
use App\Models\Category;
use Exception;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController extends BaseApiController
{
    public function __construct(
        private readonly CategoryService $CatgService,
    ) {}
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request): JsonResponse
    {
        try {   
            $perPage = (int) $request->query('per_page', 15);
            $perPage = max(1, min($perPage, 100)); // حد أقصى: 100
    
            $filters = [
                'search'    => $request->query('search'),
            ];
    
            $paginator = $this->CatgService->getAll($perPage, $filters);
    
     
            // $usersCollection = CategoryResource::collection($paginator);
    
            // لو عندك دالة successResponse، ابقي استخدمها. هنا مثال عام:
            return $this->successResponse(
                message: 'Categories retrieved successfully',
                statusCode: Response::HTTP_OK,
                data: [
                    'categories' => CategoryResource::collection($paginator),
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
                message: 'Failed to Get Categories: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(CreateCategoryRequst $request): JsonResponse
    {
        try {
           
            $data = $request->validated();
    
        
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('categories', 'public');
                $data['image'] = $path;
            }
    
            // نداء الـ service
            $created = $this->CatgService->create_category($data); // لاحظ اسم متغير service مقترح
    
            // // أضف URL للصورة للـ response لو حابب
            // if (!empty($created->image)) {
            //     $created->image_url = Storage::disk('public')->url($created->image);
            // }
    
            return $this->successResponse(
                message: 'Create Category done',
                statusCode: Response::HTTP_CREATED,
                data: $created
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                message: 'Create Category failed: ' . $e->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    /**
     * Display the specified category.
     */
    public function show($category_id): JsonResponse
    {
        try {
            // استدعيه
            $data = $this->CatgService->get_category_by_id((int)$category_id);

            return $this->successResponse(
                message: 'Category retrieved successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to get Category info: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryUpdateRequste $request, $category_id): JsonResponse
    {
      
        try {
            // استدعيه
            $data = $this->CatgService->update_category((int)$category_id, $request->all());

            return $this->successResponse(
                message: 'Category updated successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to update Category: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy($category_id): JsonResponse
    {
        try {
            // استدعيه
            $data = $this->CatgService->delete_category((int)$category_id,);

            return $this->successResponse(
                message: 'Category deleted successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to deleted Category: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display the children of the specified category.
     */
    public function children($category_id): JsonResponse
    {
  
        try {
            // استدعيه
            $data = $this->CatgService->get_children_categories((int)$category_id);

            return $this->successResponse(
                message: 'Category updated successfully',
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to update Category: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display the products of the specified category.
     */
    public function products($category_id): JsonResponse
    {
        try {
            $data = $this->CatgService->get_products_category((int)$category_id);
    
            return $this->successResponse(
                message: 'Products retrieved successfully',
                statusCode: Response::HTTP_OK,
                data: $data
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'Failed to get products: ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    
}
