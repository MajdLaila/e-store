<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;


final readonly class ProductRepositre
{
  public function __construct(
    private Product $product,
    private ProductImage $productImage,
  ) {}

   public function create_product(array $data): Product
  {
    return $this->product->create($data);
  }


  public function create_product_image(array $data): ProductImage
  {
    return $this->productImage->create($data);
  }


  public function findByIdWithImages(int $id, array $with = ['images']): Product
  {
    $product = $this->product->with($with)->find($id);

    if (!$product) {
      throw new ModelNotFoundException("Product with id {$id} not found.");
    }

    return $product;
  }


  public function update_product(Product $product, array $data): Product
  {
    $product->update($data);
    // return fresh instance
    return $product->refresh();
  }


  public function delete_product_image(int $imageId): bool
  {
    return $this->productImage->where('id', $imageId)->delete() > 0;
  }


  public function set_all_images_not_main(int $productId): int
  {
    return $this->productImage
      ->where('product_id', $productId)
      ->where('is_main', true)
      ->update(['is_main' => false]);
  }

  public function set_image_main(int $imageId): int
  {
    return $this->productImage
      ->where('id', $imageId)
      ->update(['is_main' => true]);
  }

  public function get_images_by_product(int $productId): Collection
  {
    return $this->productImage
      ->where('product_id', $productId)
      ->orderByDesc('is_main')
      ->orderBy('id')
      ->get();
  }

 
  public function delete_all_images_for_product(int $productId): int
  {
    return $this->productImage->where('product_id', $productId)->delete();
  }
}
