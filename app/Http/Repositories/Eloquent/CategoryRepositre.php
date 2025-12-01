<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 * Handles the creation and retrieval of categories.
 */
final readonly class CategoryRepositre
{
  public function __construct(
    private Category $category,
  ) {}

  public function create_category($data)
  {
    $category = $this->category->create($data);
    return $category;
  }

  public function buildQuery(array $filters = [], array $columns = []): Builder
  {
    $columns = $columns ?: [
      'id',
      'parent_id',
      'name',
      'image',
      'created_at',
      'updated_at'
    ];

    $query = $this->category->newQuery()->select($columns);

    if (!empty($filters['search'])) {
      $search = $filters['search'];
      $query->where(function (Builder $q) use ($search) {
        $q->where('name', 'like', "%{$search}%");
      });
    }

    return $query;
  }

  public function paginate(int $perPage = 15, array $filters = [], array $columns = []): LengthAwarePaginator
  {
    $perPage = max(1, min($perPage, 100)); // حماية من perPage كبير جداً
    $query = $this->buildQuery($filters, $columns);

    // لو حاب تعمل eager load على علاقات تمر عبر $filters مثلاً: if(isset($filters['with'])) { $query->with($filters['with']); }
    return $query->paginate($perPage);
  }

  public function get_category_by_id($category_id): Category
  {
    $category = $this->category->where('id', $category_id)->first();

    if (!$category) {
      throw new ModelNotFoundException('Category not found');
    }

    return $category;
  }

  public function update_category_by_id(int $category_id, array $data): Category
  {
    $category = $this->category->find($category_id);

    if (!$category) {
      throw new ModelNotFoundException('Category not found');
    }

    // هنا لا يوجد باسورد مع التصنيفات فلا داعي لمعالجتها

    $category->fill($data);
    $category->save();

    return $category;
  }

  public function delete_category_by_id(int $category_id): bool
  {
    $category = $this->category->find($category_id);

    if (!$category) {
      throw new ModelNotFoundException('Category not found');
    }

    return $category->delete();
  }

  /**
   * جلب أبناء التصنيف (children categories)
   *
   * @param int $category_id
   * @return Collection
   */
  public function get_children_categories(int $category_id): Collection
  {
    // جميع التصنيفات التي يكون parent_id لها هو $category_id
    return $this->category->where('parent_id', $category_id)->get();
  }
  public function get_products_category(int $category_id): LengthAwarePaginator
  {
    // جلب كل المنتجات التابعة لهذا التصنيف
    $category = $this->category->find($category_id);

    if (!$category) {
      throw new ModelNotFoundException('Category not found');
    }

    // تأكد أن علاقة المنتجات معرفة في الموديل
    return $category->products()->with('images')->paginate(10);
  }
}
