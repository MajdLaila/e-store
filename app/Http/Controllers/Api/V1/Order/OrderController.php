<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Order\StoreOrderRequest;
use App\Http\Services\Order\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderController extends BaseApiController
{
  public function __construct(
    private readonly OrderService $orderService,
  ) {}

  /**
   * Create a new order
   */
  public function store(StoreOrderRequest $request)
  {
    try {
      $order = $this->orderService->createOrder(
        $request->validated()
      );

      return $this->successResponse(
        message: 'Order created successfully',
        statusCode: Response::HTTP_CREATED,
        data: $order
      );
    } catch (Throwable $exception) {

      return $this->errorResponse(
        message: $exception->getMessage(),
        statusCode: Response::HTTP_BAD_REQUEST
      );
    }
  }
  public function index()
  {
    try {
      $orders = $this->orderService->getAllOrders();
      return $this->successResponse(data: $orders);
    } catch (Throwable $e) {
      return $this->errorResponse(message: $e->getMessage());
    }
  }

  // جلب كل الأوردرات ليوزر محدد
  public function getUserOrders($userId)
  {
    try {
      $orders = $this->orderService->getOrdersByUser((int) $userId);
      return $this->successResponse(data: $orders);
    } catch (Throwable $e) {
      return $this->errorResponse(message: $e->getMessage());
    }
  }

  // تعديل حالة أوردر
  public function updateStatus(Request $request, $orderId)
  {
    $request->validate([
      'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
    ]);

    try {
      $order = $this->orderService->updateOrderStatus(
        (int) $orderId,
        $request->input('status')
      );
      return $this->successResponse(
        message: 'Order status updated successfully',
        data: $order
      );
    } catch (Throwable $e) {
      return $this->errorResponse(message: $e->getMessage());
    }
  }

  // حذف أوردر
  public function destroy($orderId)
  {
    try {
      $this->orderService->deleteOrder((int) $orderId);
      return $this->successResponse(
        message: 'Order deleted successfully',
        statusCode: Response::HTTP_NO_CONTENT
      );
    } catch (Throwable $e) {
      return $this->errorResponse(message: $e->getMessage());
    }
  }
}
