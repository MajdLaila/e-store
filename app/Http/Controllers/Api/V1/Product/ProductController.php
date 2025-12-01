<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\CreateProductRequest;
use App\Http\Requests\Api\V1\Product\CreateProductRequste;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Services\Product\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Container\Attributes\Storage;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends BaseApiController
{
  public function __construct(
    private readonly ProductService $product_service,
  ) {}
  // جلب جميع المنتجات
  public function index()
  {
    // من المفترض هنا جلب جميع المنتجات مع إمكانية التصفية أو التصفح عند الحاجة
    // مثال توضيحي فقط:
    // return response()->json(Product::all());

    return response()->json([
      'message' => 'جلب جميع المنتجات (مثال)'
    ]);
  }

  // public function show($product_id): JsonResponse
  // {
  //     try {
  //         // استدعيه
  //         $data = $this->product_service->get_product_by_((int)$category_id);

  //         return $this->successResponse(
  //             message: 'Category retrieved successfully',
  //             statusCode: Response::HTTP_OK,
  //             data: $data,
  //         );
  //     } catch (Exception $exception) {
  //         return $this->errorResponse(
  //             message: 'Failed to get Category info: ' . $exception->getMessage(),
  //             statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
  //         );
  //     }
  // }

  // إنشاء منتج جديد
  public function store(CreateProductRequest $request)
  {
    try {
      $data = $request->validated();

      // اجلب ملفات الصور إن وُجدت
      $imageFiles = $request->file('images', []); // array of UploadedFile

      // نداء الـ service (مرّر البيانات والملفات)
      $created = $this->product_service->create_product($data, $imageFiles);

      return $this->successResponse(
        message: 'Create Product done',
        statusCode: Response::HTTP_CREATED,
        data: $created
      );
    } catch (Exception $e) {
      return $this->errorResponse(
        message: 'Create Product failed: ' . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }
  // تحديث منتج موجود
  public function update(UpdateProductRequest $request, int $id)
  {
    try {
      $data = $request->validated();

      $imageFiles = $request->file('images', []);
      // هون بيبعت بس صور يلي بدو يحذفا
      $removeIds = $request->input('remove_image_ids', []);
      $mainImageId = $request->input('main_image_id', null);

      $updated = $this->product_service->update_product((int)$id, $data, $imageFiles, $removeIds, $mainImageId);

      return $this->successResponse(
        message: 'Product updated',
        statusCode: Response::HTTP_OK,
        data: $updated
      );
    } catch (Exception $e) {
      return $this->errorResponse(
        message: 'Update failed: ' . $e->getMessage(),
        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }


  // حذف منتج
  public function destroy($product)
  {
    // مثال توضيحي فقط:
    // $product = Product::findOrFail($product);
    // $product->delete();

    return response()->json([
      'message' => "تم حذف المنتج رقم {$product} (مثال)"
    ]);
  }
}
