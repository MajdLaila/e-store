<?php

declare(strict_types=1);

namespace App\Http\Services\Product;

use App\Http\Repositories\Eloquent\ProductRepositre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Throwable;

final readonly class ProductService
{
  public function __construct(
    private ProductRepositre $product_Repositre,
  ) {}


  public function create_product(array $data, array $imageFiles = []): array
  {
    return DB::transaction(function () use ($data, $imageFiles) {

      $product = $this->product_Repositre->create_product($data);

      $savedImages = [];
      foreach ($imageFiles as $index => $file) {
 // لكل منتج عملتلو ملف يكون فيه صورو الخاصة فيه
        $dir = "products/{$product->id}";
        // اسم فريد
        //uniqid يمنع تكرار الأسماء حتى لو رفعت صورتين بنفس الاسم
        $filename = uniqid('img_') . '.' . $file->getClientOriginalExtension();
        // ex: products/12/img_xxx.jpg
        $path = $file->storeAs($dir, $filename, 'public');

        // أول صورة تكون الأساسية
        $isMain = ($index === 0);

        // أنشئ سجل الصورة في قاعدة البيانات
        $image = $this->product_Repositre->create_product_image([
          'product_id' => $product->id,
          'image_url' => $path,
          'is_main' => $isMain,
        ]);

        $savedImages[] = $image->toArray();
      }

// يعني يجيب كل الصور يلي تتبع لهذا المنتج الجديد.
      $product->load('images');

       $result = $product->toArray();
       // في حال صار في سيرفر مشان صور نحط رابط اول صورة رابط دومين
      // $result['images'] = array_map(function ($img) {
      //   if (!empty($img['image_url'])) {
      //     // إذا شغّلت php artisan storage:link سيكون هذا رابط عام صالح
      //     $img['image_full_url'] = Storage::disk('public')->url($img['image_url']);
      //   } else {
      //     $img['image_full_url'] = null;
      //   }
      //   return $img;
      // }, array: $result['images'] ?? []);

      return $result;
    }, attempts:2 );
    // رقم 2 قصدي عدد تكرار في حال فشل اول مرة
  }
  public function update_product(int $id, array $data, array $imageFiles = [], array $removeIds = [], ?int $mainImageId = null): array
  {
    return DB::transaction(function () use ($id, $data, $imageFiles, $removeIds, $mainImageId) {

      $product = $this->product_Repositre->findByIdWithImages($id);
      //  تحديث الحقول العامة
      $this->product_Repositre->update_product($product, $data);


      if (!empty($removeIds)) {
        $imagesToRemove = $product->images->whereIn('id', $removeIds);
        foreach ($imagesToRemove as $img) {
          // احذف الملف الفيزيائي
          Storage::disk('public')->delete($img->image_url);
          // احذف السجل
          $this->product_Repositre->delete_product_image($img->id);
        }
      }

      //  إضافة صور جديدة
      foreach ($imageFiles as $index => $file) {
        $dir = "products/{$product->id}";
        $filename = uniqid('img_') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $filename, 'public');

        $image = $this->product_Repositre->create_product_image([
          'product_id' => $product->id,
          'image_url' => $path,
          'is_main' => false,
        ]);
      }

      //    إعادة جلب الصور المحدثة
      $product->load('images');

      //  ضبط الصورة الرئيسية
      if ($mainImageId) {
        // تحقق أن الصورة تنتمي للمنتج
        if ($product->images->where('id', $mainImageId)->isEmpty()) {
          throw new \InvalidArgumentException('main_image_id does not belong to product');
        }
        // اجعل كلها false ثم فعّل المطلوبة
        $this->product_Repositre->set_all_images_not_main($product->id);
        $this->product_Repositre->set_image_main($mainImageId);
      } else {
// هون مشان اذا حذف صورة رئيسية وماضل اي صورة رئيسية منسوي اول وحدة هية رئيسية

        if ($product->images->where('is_main', true)->isEmpty()) {
          $first = $product->images->first();
          if ($first) {
            $this->product_Repositre->set_image_main($first->id);
          }
        }
      }

      $product->refresh(); // تحديث الكائن بعد التغييرات
      $product->load('images');

      $result = $product->toArray();
      // $result['images'] = array_map(function ($img) {
      //   $img['image_full_url'] = $img['image_url'] ? Storage::disk('public')->url($img['image_url']) : null;
      //   return $img;
      // }, $result['images'] ?? []);

      return $result;
    }, 2);
  }
}
