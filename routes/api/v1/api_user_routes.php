<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\UserController;

// راوت جلب معلومات المستخدم، كل اليوزرات، وتعديل وحذف المستخدم
Route::prefix('user')
    ->middleware(['auth:sanctum'])
    ->group(static function (): void {
        // GET    /api/v1/user/all      جلب كل ليوزرات (Get all users)
        Route::get('/all', [UserController::class, 'index'])->name('user.all');

        // GET    /api/v1/user          جلب معلومات المستخدم الحالي (Authenticated User Info)
        Route::get('/', [UserController::class, 'get_user_info'])->name('user.info');
        // GET    /api/v1/user/{id}     جلب معلومات مستخدم بواسطة المعرف (Get User Info by ID)
        Route::get('/{id}', [UserController::class, 'get_user_info_byid'])->name('user.info.byid');
        // PUT    /api/v1/user/{id}     تحديث بيانات مستخدم بواسطة المعرف (Update User by ID)
        Route::put('/{id}', [UserController::class, 'update_user'])->name('user.update');
        // DELETE /api/v1/user/{id}     حذف مستخدم بواسطة المعرف (Delete User by ID)
        Route::delete('/{id}', [UserController::class, 'delete_user'])->name('user.delete');
    });

