<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

final readonly class OrderRepository
{
  public function createOrder(array $data): Order
  {
    return Order::create($data);
  }

  public function createOrderItem(array $data): OrderItem
  {
    return OrderItem::create($data);
  }

  /**
   * 🔒 Lock product row to prevent race condition
   */
  public function lockProductForUpdate(int $productId): Product
  {
    return Product::where('id', $productId)
      ->lockForUpdate()
      ->firstOrFail();
  }

  public function decreaseProductStock(Product $product, int $quantity): void
  {
    $product->decrement('stock', $quantity);
  }

  public function updateOrderTotal(Order $order, float $total): void
  {
    $order->update([
      'total_price' => $total,
    ]);
  }
  public function getAllOrders()
  {
    return Order::with('items.product')->paginate(10);
  }

  public function getOrdersByUser(int $userId)
  {
    return Order::with('items.product')
      ->where('user_id', $userId)->paginate(10)
    ;
  }

  public function findOrder(int $orderId): ?Order
  {
    return Order::with('items.product')->find($orderId);
  }

  public function updateOrderStatus(Order $order, string $status): bool
  {
    return $order->update(['status' => $status]);
  }

  public function deleteOrder(Order $order): bool
  {
    return $order->delete();
  }
}
