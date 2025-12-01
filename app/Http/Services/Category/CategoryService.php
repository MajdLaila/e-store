<?php

declare(strict_types=1);

namespace App\Http\Services\Category;

use App\Http\Repositories\Eloquent\CategoryRepositre;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class CategoryService
{
    public function __construct(
        private CategoryRepositre $Category_Repositre,
    ) {}

    public function create_category(array $data): array
    {
        $category = $this->Category_Repositre->create_category($data);

        // Ensure the returned value is an array, even if a model is returned from the repository.
        if (is_object($category)) {
            // If the model has toArray, use it
            if (method_exists($category, 'toArray')) {
                return $category->toArray();
            }
            // Fallback: Cast public properties to array
            return (array) $category;
        }

        return (array) $category;
    }
    public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->Category_Repositre->paginate($perPage, $filters);
    }
    public function get_category_by_id(int $category_id): array
    {
        $category = $this->Category_Repositre->get_category_by_id($category_id);

        return [
            'Category' => $category,
        ];
    }
    public function update_category(int $category_id, array $data): array
    {
        $user = $this->Category_Repositre->update_category_by_id($category_id, $data);

        return [
            'Category' => $user,
        ];
    }
    public function delete_category(int $category_id): array
    {
        $deleted = $this->Category_Repositre->delete_category_by_id($category_id);

        return [
            'deleted' => $deleted,
        ];
    }

    public function get_children_categories(int $category_id): array
    {
        $categories = $this->Category_Repositre->get_children_categories($category_id);

        return [
            'categories' => $categories,
        ];
    }

    public function get_products_category(int $category_id): LengthAwarePaginator
    {
        return $this->Category_Repositre->get_products_category($category_id);

        
    }
}
