<?php

declare(strict_types=1);

namespace App\Http\Services\Order;

use App\Http\Repositories\Eloquent\OrderRepository;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class OrderService
{
  public function __construct(
    private OrderRepository $orderRepository,
  ) {}

  public function createOrder(array $data)
  {
    return DB::transaction(function () use ($data) {

      // 1️⃣ Create order shell
      $order = $this->orderRepository->createOrder([
        'user_id' => auth()->id(),
        'status' => 'pending',
        'shipping_address' => $data['shipping_address'],
        'phone' => $data['phone'],
      ]);

      $totalPrice = 0;

      foreach ($data['items'] as $item) {

        // 2️⃣ LOCK product row
        /*
        هون سطر المنتج يتم قفله لحتى نخلص لعملية عليه مشان ما حدا يطلب نفس لمنتج لحتى نخلص عليه لعمليات
        */
        $product = $this->orderRepository
          ->lockProductForUpdate($item['product_id']);


        // 3️⃣ Check stock
        if ($product->stock < $item['quantity']) {
          throw new RuntimeException(
            "Insufficient stock for product: {$product->name}"
          );
        }

        $subtotal = $product->price * $item['quantity'];
        $totalPrice += $subtotal;

        // 4️⃣ Create order item
        $this->orderRepository->createOrderItem([
          'order_id' => $order->id,
          'product_id' => $product->id,
          'quantity' => $item['quantity'],
          'price' => $product->price,
          'total' => $subtotal,
        ]);

        // 5️⃣ Decrease stock safely
        $this->orderRepository->decreaseProductStock(
          $product,
          $item['quantity']
        );
      }

      // 6️⃣ Update order total
      $this->orderRepository->updateOrderTotal(
        $order,
        $totalPrice
      );

      return $order->load('items.product');
    });
  }
  public function getAllOrders()
  {
    return $this->orderRepository->getAllOrders();
  }

  public function getOrdersByUser(int $userId)
  {
    return $this->orderRepository->getOrdersByUser($userId);
  }

  public function updateOrderStatus(int $orderId, string $status): Order
  {
    $order = $this->orderRepository->findOrder($orderId);

    if (!$order) {
      throw new RuntimeException("Order not found.");
    }

    $this->orderRepository->updateOrderStatus($order, $status);

    return $order;
  }

  public function deleteOrder(int $orderId): void
  {
    $order = $this->orderRepository->findOrder($orderId);

    if (!$order) {
      throw new RuntimeException("Order not found.");
    }

    $this->orderRepository->deleteOrder($order);
  }
}
